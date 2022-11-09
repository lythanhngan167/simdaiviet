<?php

namespace RtclPro\Api;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

abstract class RestController
{
    protected $namespace;

    protected $rest_base;

    protected $schema;

    public function register_routes() {
        _doing_it_wrong(
            'RtclRestController::register_routes',
            /* translators: %s: register_routes() */
            sprintf(__("Method '%s' must be overridden."), __METHOD__),
            '4.7'
        );
    }

    /**
     * Prepares one item for create or update operation.
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return object|WP_Error The prepared item, or WP_Error object on failure.
     * @since 4.7.0
     *
     */
    protected function prepare_item_for_database($request) {
        return new WP_Error(
            'invalid-method',
            /* translators: %s: Method name. */
            sprintf(__("Method '%s' not implemented. Must be overridden in subclass."), __METHOD__),
            array('status' => 405)
        );
    }

    /**
     * Retrieves the item's schema for display / public consumption purposes.
     *
     * @return array Public item schema data.
     */
    public function get_public_item_schema() {

        $schema = $this->get_item_schema();

        if (!empty($schema['properties'])) {
            foreach ($schema['properties'] as &$property) {
                unset($property['arg_options']);
            }
        }

        return $schema;
    }


    /**
     * Retrieves the query params for the collections.
     *
     * @return array Query parameters for the collection.
     *
     */
    public function get_collection_params() {
        return [
            'page'     => [
                'description'       => esc_html__('Current page of the collection.', 'classified-listing-pro'),
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint',
                'validate_callback' => 'rest_validate_request_arg',
                'minimum'           => 1,
            ],
            'per_page' => [
                'description'       => esc_html__('Maximum number of items to be returned in result set.', 'classified-listing-pro'),
                'type'              => 'integer',
                'default'           => 10,
                'minimum'           => 1,
                'maximum'           => 100,
                'sanitize_callback' => 'absint',
                'validate_callback' => 'rest_validate_request_arg',
            ],
            'search'   => [
                'description'       => esc_html__('Limit results to those matching a string.', 'classified-listing-pro'),
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ],
            'offset'   => [
                'description' => esc_html__('Offset the result set by a specific number of items.', 'classified-listing-pro'),
                'type'        => 'integer',
            ],
            'order'    => [
                'description' => esc_html__('Order sort attribute ascending or descending.', 'classified-listing-pro'),
                'type'        => 'string',
                'default'     => 'desc',
                'enum'        => [
                    'asc',
                    'desc',
                ],
            ],
            'orderby'  => [
                'description' => esc_html__('Sort collection by object attribute.', 'classified-listing-pro'),
                'type'        => 'string',
                'default'     => 'date_gmt',
                'enum'        => [
                    'date',
                    'date_gmt',
                    'id',
                    'include',
                    'post',
                    'parent',
                    'type',
                ],
            ]
        ];
    }


    /**
     * Prepares a response for insertion into a collection.
     *
     * @param WP_REST_Response $response Response object.
     *
     * @return WP_REST_Response|array Response data, ready for insertion into collection data.
     *
     */
    public function prepare_response_for_collection($response) {
        if (!($response instanceof WP_REST_Response)) {
            return $response;
        }

        $data = (array)$response->get_data();
        $server = rest_get_server();
        $links = $server::get_compact_response_links($response);

        if (!empty($links)) {
            $data['_links'] = $links;
        }

        return $data;
    }

    /**
     * Retrieves the item's schema, conforming to JSON Schema.
     *
     * @return array Item schema data.
     *
     */
    public function get_item_schema() {
        return $this->add_additional_fields_schema(array());
    }

    /**
     * Adds the schema from additional fields to a schema array.
     *
     * The type of object is inferred from the passed schema.
     *
     * @param array $schema Schema array.
     *
     * @return array Modified Schema array.
     *
     */
    protected function add_additional_fields_schema($schema) {
        if (empty($schema['title'])) {
            return $schema;
        }

        // Can't use $this->get_object_type otherwise we cause an inf loop.
        $object_type = $schema['title'];

        $additional_fields = $this->get_additional_fields($object_type);

        foreach ($additional_fields as $field_name => $field_options) {
            if (!$field_options['schema']) {
                continue;
            }

            $schema['properties'][$field_name] = $field_options['schema'];
        }

        return $schema;
    }


    /**
     * Retrieves all of the registered additional fields for a given object-type.
     *
     * @param string $object_type Optional. The object type.
     *
     * @return array Registered additional fields (if any), empty array if none or if the object type could
     *               not be inferred.
     *
     */
    protected function get_additional_fields($object_type = null) {

        if (!$object_type) {
            $object_type = $this->get_object_type();
        }

        if (!$object_type) {
            return array();
        }

        global $rtcl_rest_additional_fields;

        if (!$rtcl_rest_additional_fields || !isset($rtcl_rest_additional_fields[$object_type])) {
            return array();
        }

        return $rtcl_rest_additional_fields[$object_type];
    }


    /**
     * Retrieves the object type this controller is responsible for managing.
     *
     * @return string Object type for the controller.
     *
     */
    protected function get_object_type() {
        $schema = $this->get_item_schema();

        if (!$schema || !isset($schema['title'])) {
            return null;
        }

        return $schema['title'];
    }

    /**
     * Gets an array of fields to be included on the response.
     *
     * Included fields are based on item schema and `_fields=` request argument.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return string[] Fields to be included in the response.
     *
     */
    public function get_fields_for_response($request) {

        $schema = $this->get_item_schema();
        $properties = isset($schema['properties']) ? $schema['properties'] : array();

        $additional_fields = $this->get_additional_fields();

        foreach ($additional_fields as $field_name => $field_options) {
            // For back-compat, include any field with an empty schema
            // because it won't be present in $this->get_item_schema().
            if (is_null($field_options['schema'])) {
                $properties[$field_name] = $field_options;
            }
        }

        // Exclude fields that specify a different context than the request context.
        $context = $request['context'];
        if ($context) {
            foreach ($properties as $name => $options) {
                if (!empty($options['context']) && !in_array($context, $options['context'], true)) {
                    unset($properties[$name]);
                }
            }
        }

        $fields = array_keys($properties);

        if (!isset($request['_fields'])) {
            return $fields;
        }
        $requested_fields = wp_parse_list($request['_fields']);
        if (0 === count($requested_fields)) {
            return $fields;
        }
        // Trim off outside whitespace from the comma delimited list.
        $requested_fields = array_map('trim', $requested_fields);
        // Always persist 'id', because it can be needed for add_additional_fields_to_object().
        if (in_array('id', $fields, true)) {
            $requested_fields[] = 'id';
        }
        // Return the list of all requested fields which appear in the schema.
        return array_reduce(
            $requested_fields,
            function ($response_fields, $field) use ($fields) {
                if (in_array($field, $fields, true)) {
                    $response_fields[] = $field;
                    return $response_fields;
                }
                // Check for nested fields if $field is not a direct match.
                $nested_fields = explode('.', $field);
                // A nested field is included so long as its top-level property
                // is present in the schema.
                if (in_array($nested_fields[0], $fields, true)) {
                    $response_fields[] = $field;
                }
                return $response_fields;
            },
            array()
        );
    }
}