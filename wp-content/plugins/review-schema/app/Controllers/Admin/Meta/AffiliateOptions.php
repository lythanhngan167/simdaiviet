<?php
namespace Rtrs\Controllers\Admin\Meta;
use Rtrs\Helpers\Functions; 

class AffiliateOptions { 

    /**
     * Marge all meta field
     *
     * @return array
     */
    function allMetaFields() { 
        $fields = array();
        $fieldsA = array_merge(  
            $this->sectionAffiliateFields(), 
            $this->sectionSchemaFields(), 
            $this->sectionStyleFields()
        );
        foreach ($fieldsA as $field) {
            $fields[] = $field;
        } 

        return $fields;
    }

    function sectionAffiliateFields() {  
        $section_affiliate = array(  
                 
            array(
                "type"    => "radio-image",
                "name"    => "layout",
                "label"   => esc_html__("Layout", 'review-schema'),
                'default' => "two",
                "id"      => "rtrs-affiliate_layout",
                "options" => array(
                    array(
                        'value' => 'two',
                        'img' => RTRS_URL . '/assets/imgs/summary-two.jpg', 
                    ),
                    array(
                        'value' => 'one',
                        'img' => RTRS_URL . '/assets/imgs/summary-one.jpg',
                        'is_pro' => true,   
                    ), 
                )
            ),       
            array(
                "type"        => "text",
                "name"        => "title",
                "holderClass" => "rtrs-product",
                "id"          => "rtrs-product-title",
                "label"       => esc_html__("Title", 'review-schema'), 
            ),  
            array(
                "type"        => "textarea",
                "name"        => "short_desc",
                "label"       => esc_html__("Short description", 'review-schema'),
                "holderClass" => "rtrs-product",
                "id"          => "rtrs-summary-field", 
            ), 
            array(
                'name'    => 'featured_image',
                'type'     => 'image',
                'is_pro' => true,
                "holderClass" => "rtrs-product",
                'label'    => esc_html__('Featured Image', "review-schema"), 
            ),
            array(
                "type"    => "group",
                "name"    => "rating_criteria",  
                "id"      => "rtrs-affiliate_criteria",
                'is_pro' => true,
                "label"   => esc_html__("Rating criteria", 'review-schema'),  
                "fields" => [
                    array(
                        "name"   => "title",
                        "type"   => "text",
                        "label"  => esc_html__("Criteria name", 'review-schema'),  
                    ), 
                    array(
                        "type"    => "select",
                        "name"    => "type",
                        "label"   => esc_html__("Rating type", 'review-schema'), 
                        'default' => 'rating',
                        "options" => array(
                            "rating"  => esc_html__("Rating (1-5)", 'review-schema'), 
                            "percent"  => esc_html__("Percent (1-100)", 'review-schema'), 
                        )
                    ), 
                    array(
                        "name"   => "avg",
                        "type"   => "float",
                        "label"  => esc_html__("Criteria value", 'review-schema'),  
                        "desc"  => esc_html__("Rating value will be 1-5 or percent 1-100", 'review-schema'),  
                    ), 
                ]
            ), 
            array(
                "type"        => "text",
                "name"        => "total_rating",
                "label"       => esc_html__("Total rating", 'review-schema'), 
            ),  
            array(
                "type"        => "text",
                "name"        => "avg_rating",
                "label"       => esc_html__("Avg rating", 'review-schema'), 
            ), 
            array(
                "type"        => "switch",
                "name"        => "enable_pros", 
                "id"          => "enable_pros",
                "label"       => esc_html__("Allow pros?", 'review-schema'), 
                "option"      => esc_html__("Enable", 'review-schema')
            ), 
            array(
                "type"      => "repeater",
                "name"      => "pros",
                "label"     => esc_html__("Pros", 'review-schema'), 
                "id"        => "rtrs-pros", 
                "alignment" => "vertical",
                "default"   => $this->prosCons(),
                "options"   => $this->prosCons()
            ), 
            array(
                "type"        => "switch",
                "name"        => "enable_cons", 
                "id"          => "enable_cons",
                "label"       => esc_html__("Allow cons?", 'review-schema'), 
                "option"      => esc_html__("Enable", 'review-schema')
            ), 
            array(
                "type"      => "repeater",
                "name"      => "cons",
                "label"     => esc_html__("Cons", 'review-schema'), 
                "id"        => "rtrs-cons", 
                "alignment" => "vertical",
                "default"   => $this->prosCons(),
                "options"   => $this->prosCons()
            ), 
            array(
                "type"        => "text",
                "name"        => "btn_txt",
                "id"          => "rtrs-btn-text",
                "label"       => esc_html__("Button text", 'review-schema'), 
            ),  
            array(
                "type"        => "url",
                "name"        => "btn_url",
                "label"       => esc_html__("Button URL", 'review-schema'), 
            ),  
            array(
                "type"        => "text",
                "name"        => "regular_price",
                "label"       => esc_html__("Regular price", 'review-schema'), 
            ),  
            array(
                "type"        => "text",
                "name"        => "offer_price",
                "label"       => esc_html__("Offer price", 'review-schema'), 
            ),  
        );
        return apply_filters('rtrs_section_affiliate_fields', $section_affiliate);
    } 
    
    function sectionSchemaFields() {  
        $prefix = 'schema';
        $section_schema =  array(   
            array(
                "type"    => "select2",
                "name"    => "rich_snippet",
                "label"   => esc_html__("Google Schema JSON-LD", 'review-schema'),
                'default' => "",
                "id"      => "rtrs-snippet-custom",
                "options" => array(
                    "" => esc_html__( 'Disable', 'review-schema' ),
                    "custom" =>  esc_html__( 'Enable', 'review-schema' ),
                )
            ),
            array(
                'name'     => 'name',
                'type'     => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label'    => esc_html__('Name', "review-schema"),
                'required' => true,
            ),
            array(
                'name'  => 'image',
                'type'  => 'image',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Image', "review-schema"),
            ),
            array(
                'name'  => 'description',
                'type'  => 'textarea',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Description', "review-schema"),
                'desc'  => esc_html__("Product description.", "review-schema")
            ),
            array(
                'name'  => 'identifier_section',
                'type'  => 'heading',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Product Identifier', "review-schema"),
                // 'desc'  => esc_html__("Add Product unique Identifier.", "review-schema")
            ),
            array(
                'name'        => 'sku',
                'type'        => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label'       => esc_html__('SKU', "review-schema"),
                'recommended' => true
            ),
            array(
                'name'     => 'brand',
                'type'     => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label'    => esc_html__('Brand', "review-schema"),
                'required' => true,
                'desc'     => esc_html__("The brand of the product (Used globally).", "review-schema")
            ),
            array(
                'name'     => 'identifier_type',
                'type'     => 'select',
                "holderClass" => "rtrs-snippet-custom",
                'label'    => esc_html__('Identifier Type', "review-schema"),
                'required' => true,
                'options'  => array(
                    'mpn'    => esc_html__('MPN', 'review-schema'),
                    'isbn'   => esc_html__('ISBN', 'review-schema'),
                    'gtin8'  => esc_html__('GTIN-8 (UPC, JAN)', 'review-schema'),
                    'gtin12' => esc_html__('GTIN-12 (UPC)', 'review-schema'),
                    'gtin13' => esc_html__('GTIN-13 (EAN,JAN)', 'review-schema')
                ),
                'desc' => wp_kses( __("<strong>MPN</strong><br>
                    &#8594; MPN(Manufacturer Part Number) Used globally, Alphanumeric digits (various lengths)<br>
                    <strong>GTIN</strong><br>
                    &#8594; UPC(Universal Product Code) Used in primarily North America. 12 numeric digits. eg. 892685001003.<br>
                    &#8594; EAN(European Article Number) Used primarily outside of North America. Typically 13 numeric digits (can occasionally be either eight or 14 numeric digits). eg. 4011200296908<br>
                    &#8594; ISBN(International Standard Book Number) Used globally, ISBN-13 (recommended), 13 numeric digits 978-0747595823<br>
                    &#8594; JAN(Japanese Article Number) Used only in Japan, 8 or 13 numeric digits.", "review-schema"), [ 'br' => [], 'strong' => [] ]
                )
            ),
            array(
                'name'     => 'identifier',
                'type'     => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label'    => esc_html__('Identifier Value', "review-schema"),
                'required' => true,
                'desc'     => esc_html__("Enter product unique identifier", "review-schema")
            ),
            array(
                'name'  => 'rating_section',
                'type'  => 'heading',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Product Review & Rating', "review-schema"),
            ),
            array(
                'name'        => 'reviewRatingValue',
                'type'        => 'number',
                "holderClass" => "rtrs-snippet-custom",
                'label'       => esc_html__('Review rating avg value', "review-schema"),
                'recommended' => true,
                'desc'        => esc_html__("Rating value. (1 , 2.5, 3, 5 etc)", "review-schema")
            ),
            array(
                'name'        => 'reviewBestRating',
                'type'        => 'number',
                "holderClass" => "rtrs-snippet-custom",
                'label'       => esc_html__('Review Best rating', "review-schema"),
                'recommended' => true,
            ),
            array(
                'name'        => 'reviewWorstRating',
                'type'        => 'number',
                "holderClass" => "rtrs-snippet-custom",
                'label'       => esc_html__('Review Worst rating', "review-schema"),
                'recommended' => true,
            ),
            array(
                'name'  => 'reviewAuthor',
                'type'  => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Review author', "review-schema"),
            ),
            array(
                'name'        => 'ratingValue',
                'type'        => 'number',
                "holderClass" => "rtrs-snippet-custom",
                'label'       => esc_html__('Aggregate Rating value', "review-schema"),
                'recommended' => true,
                'desc'        => esc_html__("Rating value. (1 , 2.5, 3, 5 etc)", "review-schema")
            ),
            array(
                'name'  => 'reviewCount',
                'type'  => 'number',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Aggregate Total review count', "review-schema"),
                'desc'  => wp_kses( __("Review Count. <span class='required'>This is required if (Rating value) is given</span>", "review-schema"), [ 'span' => [] ] )
            ),
            array(
                'name'  => 'pricing_section',
                'type'  => 'heading',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Product Pricing', "review-schema"),
            ),
            array(
                'name'  => 'priceCurrency',
                'type'  => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Price currency', "review-schema"),
                'desc'  => esc_html__("The 3-letter currency code.", "review-schema")
            ),
            array(
                'name'  => 'price',
                'type'  => 'number',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Price', "review-schema"),
                'desc'  => esc_html__("The lowest available price, including service charges and fees, of this type of ticket.", "review-schema")
            ),
            array(
                'name'        => 'priceValidUntil',
                'type'        => 'text',
                "holderClass" => "rtrs-snippet-custom",
                'label'       => esc_html__('PriceValidUntil', "review-schema"),
                'recommended' => true,
                'class'       => 'rtrs-date',
                'desc'        => esc_html__("The date (in ISO 8601 date format) after which the price will no longer be available. Like this: 2020-12-25 14:20:00", "review-schema")
            ),
            array(
                'name'    => 'availability',
                'type'    => 'select',
                "holderClass" => "rtrs-snippet-custom",
                'label'   => esc_html__('Availability', "review-schema"),
                'empty'   => esc_html__( 'Select one', 'review-schema' ),
                'options' => array(
                    'https://schema.org/InStock'             => esc_html__('InStock', "review-schema"),
                    'https://schema.org/InStoreOnly'         => esc_html__('InStoreOnly', "review-schema"),
                    'https://schema.org/OutOfStock'          => esc_html__('OutOfStock', "review-schema"),
                    'https://schema.org/SoldOut'             => esc_html__('SoldOut', "review-schema"),
                    'https://schema.org/OnlineOnly'          => esc_html__('OnlineOnly', "review-schema"),
                    'https://schema.org/LimitedAvailability' => esc_html__('LimitedAvailability', "review-schema"),
                    'https://schema.org/Discontinued'        => esc_html__('Discontinued', "review-schema"),
                    'https://schema.org/PreOrder'            => esc_html__('PreOrder', "review-schema"),
                ),
                'desc'    => esc_html__("Select a availability type", "review-schema")
            ),
            array(
                'name'    => 'itemCondition',
                'type'    => 'select',
                "holderClass" => "rtrs-snippet-custom",
                'label'   => esc_html__( 'Product condition', 'review-schema' ),  
                'empty'   => esc_html__( 'Select one', 'review-schema' ),
                'options' => array(
                    'https://schema.org/NewCondition'         => esc_html__('NewCondition', "review-schema"),
                    'https://schema.org/UsedCondition'        => esc_html__('UsedCondition', "review-schema"),
                    'https://schema.org/DamagedCondition'     => esc_html__('DamagedCondition', "review-schema"),
                    'https://schema.org/RefurbishedCondition' => esc_html__('RefurbishedCondition', "review-schema"),
                ),
                'desc'    => esc_html__("Select a condition", "review-schema")
            ),
            array(
                'name'  => 'url',
                'type'  => 'url',
                "holderClass" => "rtrs-snippet-custom",
                'label' => esc_html__('Product URL', "review-schema"),
                'desc'  => esc_html__("A URL to the product web page (that includes the Offer). (Don't use offerURL for markup that appears on the product page itself.)", "review-schema")
            )
        );
        return apply_filters('rtrs_section_schema_fields', $section_schema);
    } 

    function sectionStyleFields() { 
        $style_fields = array(
            array(
                "name"        => "parent_class",
                "type"        => "text",
                "label"       => "Parent class",
                "id"          => "rtrs-parent-class",
                "class"       => "medium-text", 
                "desc" => esc_html__("Parent class for adding custom css", 'review-schema')
            ), 
            array(
                "name"        => "width",
                "id"          => "rtrs-width",
                "type"        => "text",
                "class"       => "small-width",
                "label"       => esc_html__("Width", 'review-schema'),
                "desc" => esc_html__("Layout width, Like: 400px or 50% etc", 'review-schema')
            ), 
            array(
                "name"        => "product_title",
                'type'        => 'style',
                'label'       => esc_html__( 'Product title', 'review-schema' ),
            ), 
            array(
                "name"        => "product_desc",
                'type'        => 'style',
                'label'       => esc_html__( 'Product description', 'review-schema' ),
            ), 
            array(
                "name"        => "style_regular_price",
                'type'        => 'style',
                'label'       => esc_html__( 'Regular Price', 'review-schema' ),
            ), 
            array(
                "name"        => "style_offer_price",
                'type'        => 'style',
                'label'       => esc_html__( 'Offer Price', 'review-schema' ),
            ),  
            array(
                "name"  => "border_color",  
                "type"  => "color",
                "id"  => "border_color",  
                "label" => esc_html__("Border Color", "review-schema"), 
            ), 
            array(
                "name"        => "border_size",
                "id"          => "rtrs-border_size",
                "type"        => "text",
                "class"       => "small-width",
                "label"       => esc_html__("Border Size", 'review-schema'),
                "desc" => esc_html__("Pass value like (2px)", 'review-schema')
            ), 
            array(
                "name"        => "border_radius",
                "id"          => "rtrs-border_radius",
                "type"        => "text",
                "class"       => "small-width",
                "label"       => esc_html__("Border Radius", 'review-schema'),
                "desc" => esc_html__("Pass value like (5px, 5%)", 'review-schema')
            ), 
            array(
                "name"        => "circle_fill_color",
                "id"          => "rtrs-circle_fill_color",
                "type"        => "color",
                "is_pro"      => true,
                "label"       => esc_html__("Circle Fill Color", 'review-schema'), 
            ),  
            array(
                "name"        => "circle_empty_color",
                "id"          => "rtrs-circle_empty_color",
                "type"        => "color",
                "is_pro"      => true,
                "label"       => esc_html__("Circle Empty Color", 'review-schema'), 
            ),
            array(
                "name"        => "circle_border_width",
                "id"          => "rtrs-circle_border_width",
                "type"        => "number",
                "class"       => "small-width",
                "is_pro"      => true,
                "label"       => esc_html__("Circle Border Width", 'review-schema'),
                "desc" => esc_html__("Value will be generate as pixel, Give only integer like (5)", 'review-schema')
            ), 

            array(
                "name"        => "btn",
                'type'        => 'style',
                'label'       => esc_html__( 'Button', 'review-schema' ),
            ),
            array(
                "name"  => "btn_bg",  
                "type"  => "color",
                "id"  => "btn_bg",  
                "label" => esc_html__("Button Background", "review-schema"), 
            ), 
            array(
                "name"  => "btn_border_color",  
                "type"  => "color",
                "id"  => "btn_border_color",  
                "label" => esc_html__("Button Border Color", "review-schema"), 
            ), 
            array(
                "name"        => "btn_hover",
                'type'        => 'style',
                'label'       => esc_html__( 'Button Hover', 'review-schema' ),
            ),
            array(
                "name"  => "btn_hover_bg",  
                "type"  => "color",
                "id"  => "btn_hover_bg",
                "label" => esc_html__("Button Hover Background", "review-schema"), 
            ), 
            array(
                "name"  => "btn_border_hover_color",  
                "type"  => "color",
                "id"  => "btn_border_hover_color",  
                "label" => esc_html__("Button Border Hover Color", "review-schema"), 
            ), 
        );
        return apply_filters('rtrs_section_style_fields', $style_fields);
    }

    function prosCons() { 
        return apply_filters('rtrs_pros_cons', array("") );
    } 
}