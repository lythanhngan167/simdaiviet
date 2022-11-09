<?php

namespace Rtrs\Controllers\Admin;  

use Rtrs\Helpers\Functions;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ReviewTable extends \WP_List_Table {

	/** Class constructor */
	public function __construct() {
        
		parent::__construct( [
			'singular' => esc_html__( 'Review', 'review-schema' ), //singular name of the listed records
			'plural'   => esc_html__( 'Reviews', 'review-schema' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] ); 
	}   

	/**
     * @return object
     */
    public function get_comment_count( ) {
        global $wpdb;

        $where = $wpdb->prepare( 'WHERE comment_type = %s', 'review' );

        $totals = (array) $wpdb->get_results(
            "
			SELECT comment_approved, COUNT( * ) AS total
			FROM {$wpdb->comments}
			{$where}
			GROUP BY comment_approved
		",
            ARRAY_A
        );

        $comment_count = array(
            'approved'            => 0,
            'moderated'           => 0,
            'spam'                => 0,
            'trash'               => 0,
            'post-trashed'        => 0,
            'total_comments'      => 0,
            'all'                 => 0,
        );

        if ( $totals ) {
            foreach ( $totals as $row ) {
                switch ( $row['comment_approved'] ) {
                    case 'trash':
                        $comment_count['trash'] = $row['total'];
                        break;
                    case 'post-trashed':
                        $comment_count['post-trashed'] = $row['total'];
                        break;
                    case 'spam':
                        $comment_count['spam']            = $row['total'];
                        $comment_count['total_comments'] += $row['total'];
                        break;
                    case '1':
                        $comment_count['approved']        = $row['total'];
                        $comment_count['total_comments'] += $row['total'];
                        $comment_count['all']            += $row['total'];
                        break;
                    case '0':
                        $comment_count['moderated'] = $row['total'];
                        $comment_count['total_comments']     += $row['total'];
                        $comment_count['all']                += $row['total'];
                        break;
                    default:
                        break;
                }
            }
        }

        return (object) $comment_count;
    }
 
	/**
     * @global int $post_id
     * @global string $comment_status
     * @global string $comment_type
     */
    protected function get_views() {

        // global $post_id, $comment_status, $comment_type;
        global $post_id, $comment_type;
		$comment_status = isset( $_GET['comment_status'] ) ? sanitize_text_field( $_GET['comment_status'] ) : 'all';

        $status_links = array();
        $num_comments = $this->get_comment_count();

        $stati = array(
            /* translators: %s: Number of comments. */
            'all' => _nx_noop(
                'All <span class="count">(%s)</span>',
                'All <span class="count">(%s)</span>',
                'comments',
                'review-schema'
            ), // Singular not used. 

            /* translators: %s: Number of comments. */
            'moderated' => _nx_noop(
                'Pending <span class="count">(%s)</span>',
                'Pending <span class="count">(%s)</span>',
                'comments',
                'review-schema'
            ),

            /* translators: %s: Number of comments. */
            'approved' => _nx_noop(
                'Approved <span class="count">(%s)</span>',
                'Approved <span class="count">(%s)</span>',
                'comments',
                'review-schema'
            ),

            /* translators: %s: Number of comments. */
            'spam' => _nx_noop(
                'Spam <span class="count">(%s)</span>',
                'Spam <span class="count">(%s)</span>',
                'comments',
                'review-schema'
            ),

            /* translators: %s: Number of comments. */
            'trash' => _nx_noop(
                'Trash <span class="count">(%s)</span>',
                'Trash <span class="count">(%s)</span>',
                'comments',
                'review-schema'
            ),
        );

        if ( ! EMPTY_TRASH_DAYS ) {
            unset( $stati['trash'] );
        }
        
        $link = admin_url('admin.php?page=rtrs-reviews');

        if ( ! empty( $comment_type ) && 'all' != $comment_type ) {
            $link = add_query_arg( 'comment_type', $comment_type, $link );
        }

        foreach ( $stati as $status => $label ) {
            $current_link_attributes = '';

            if ( $status === $comment_status ) {
                $current_link_attributes = ' class="current" aria-current="page"';
            }

            if ( 'mine' === $status ) {
                $current_user_id    = get_current_user_id();
                $num_comments->mine = get_comments(
                    array(
                        'post_id' 		=> $post_id ? $post_id : 0,
                        'user_id' 		=> $current_user_id,
                        'count'   		=> true,
                    )
                );
                $link = add_query_arg( 'user_id', $current_user_id, $link );
            } else {
                $link = remove_query_arg( 'user_id', $link );
            }

            if ( ! isset( $num_comments->$status ) ) {
                $num_comments->$status = 10;
            }
            $link = add_query_arg( 'comment_status', $status, $link );
            if ( $post_id ) {
                $link = add_query_arg( 'p', absint( $post_id ), $link );
            } 
             
            $status_links[ $status ] = "<a href='$link'$current_link_attributes>" . sprintf(
                    translate_nooped_plural( $label, $num_comments->$status ),
                    sprintf(
                        '<span class="%s-count">%s</span>',
                        ( 'moderated' === $status ) ? 'pending' : $status,
                        number_format_i18n( $num_comments->$status )
                    )
                ) . '</a>';
        }

        /**
         * Filters the comment status links.
         *
         * @since 2.5.0 
         *
         * @param string[] $status_links An associative array of fully-formed comment status links. Includes 'All', 'Mine', 'Pending', 'Approved', 'Spam', and 'Trash'.
         */
        return $status_links;
    }

	/** Text displayed when no customer data is available */
	public function no_items() {
		esc_html_e( 'No reviews avaliable.', 'review-schema' );
	}  

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      	=> '<input type="checkbox" />',
			'review'    => esc_html__( 'Comment', 'review-schema' ),
			'post_type' => esc_html__( 'Post Type', 'review-schema' ), 
			'rating'    => esc_html__( 'Rating', 'review-schema' ),
			'status'    => esc_html__( 'Status', 'review-schema' ),
			'info' 	    => esc_html__( 'Review Info', 'review-schema' ), 
			'date'      => esc_html__( 'Date', 'review-schema' ),
		];

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'review' => array( 'review', false ),
			'post_type' => array( 'post_type', false ),
			'author' => array( 'author', false ),
			'rating' => array( 'rating', false ),
			'status' => array( 'status', false ),
			'date' => array( 'date', false ),
		);

		return $sortable_columns;
	} 

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="delete_reviews[]" value="%s" />', esc_attr( $item['id'] )
		);
	} 

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_review( $item ) { 

		global $comment_status;

        $comment = get_comment($item['id']);

        $the_comment_status = wp_get_comment_status( $comment ); 

        $out = '';

        $del_nonce     = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment->comment_ID" ) );
        $approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment->comment_ID" ) );

        $url = "comment.php?c=$comment->comment_ID";

        $approve_url   = esc_url( $url . "&action=approvecomment&$approve_nonce" );
        $unapprove_url = esc_url( $url . "&action=unapprovecomment&$approve_nonce" );
        $spam_url      = esc_url( $url . "&action=spamcomment&$del_nonce" );
        $unspam_url    = esc_url( $url . "&action=unspamcomment&$del_nonce" );
        $trash_url     = esc_url( $url . "&action=trashcomment&$del_nonce" );
        $untrash_url   = esc_url( $url . "&action=untrashcomment&$del_nonce" );
        $delete_url    = esc_url( $url . "&action=deletecomment&$del_nonce" );

        // Preorder it: Approve | Reply | Quick Edit | Edit | Spam | Trash.
        $actions = array(
            'approve'   => '',
            'unapprove' => '',
            'reply'     => '',
            'quickedit' => '',
            'edit'      => '',
            'spam'      => '',
            'unspam'    => '',
            'trash'     => '',
            'untrash'   => '',
            'delete'    => '',
        );

        // Not looking at all comments.
        if ( $comment_status && 'all' != $comment_status ) {
            if ( 'approved' === $the_comment_status ) {
                $actions['unapprove'] = sprintf(
                    '<a href="%s" data-wp-lists="%s" class="vim-u vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                    $unapprove_url,
                    "delete:the-comment-list:comment-{$comment->comment_ID}:e7e7d3:action=dim-comment&amp;new=unapproved",
                    esc_attr__( 'Unapprove this comment', 'review-schema' ),
                    esc_html__( 'Unapprove', 'review-schema' )
                );
            } elseif ( 'unapproved' === $the_comment_status ) {
                $actions['approve'] = sprintf(
                    '<a href="%s" data-wp-lists="%s" class="vim-a vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                    $approve_url,
                    "delete:the-comment-list:comment-{$comment->comment_ID}:e7e7d3:action=dim-comment&amp;new=approved",
                    esc_attr__( 'Approve this comment', 'review-schema' ),
                    esc_html__( 'Approve', 'review-schema' )
                );
            }
        } else {
			
			if ( 'unapproved' === $the_comment_status ) { 
				// Todo: approve to reply for test
				$actions['reply'] = sprintf(
					'<a href="%s" data-wp-lists="%s" class="vim-a aria-button-if-js" aria-label="%s">%s</a>',
					$approve_url,
					"dim:the-comment-list:comment-{$comment->comment_ID}:unapproved:e7e7d3:e7e7d3:new=approved",
					esc_attr__( 'Approve this comment','review-schema' ),
					esc_html__( 'Approve', 'review-schema' )
				);
			} elseif ( 'approved' === $the_comment_status ) {
				$actions['unapprove'] = sprintf(
					'<a href="%s" data-wp-lists="%s" class="vim-u aria-button-if-js" aria-label="%s">%s</a>',
					$unapprove_url,
					"dim:the-comment-list:comment-{$comment->comment_ID}:unapproved:e7e7d3:e7e7d3:new=unapproved",
					esc_attr__( 'Unapprove this comment', 'review-schema' ),
					esc_html__( 'Unapprove', 'review-schema' )
				);
			}
        }

        if ( 'spam' !== $the_comment_status ) {
            $actions['spam'] = sprintf(
                '<a href="%s" data-wp-lists="%s" class="vim-s vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                $spam_url,
                "delete:the-comment-list:comment-{$comment->comment_ID}::spam=1",
                esc_attr__( 'Mark this comment as spam', 'review-schema' ),
                /* translators: "Mark as spam" link. */
                esc_html_x( 'Spam', 'verb', 'review-schema' )
            );
        } elseif ( 'spam' === $the_comment_status ) {
            $actions['unspam'] = sprintf(
                '<a href="%s" data-wp-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                $unspam_url,
                "delete:the-comment-list:comment-{$comment->comment_ID}:66cc66:unspam=1",
                esc_attr__( 'Restore this comment from the spam', 'review-schema' ),
                esc_html_x( 'Not Spam', 'comment', 'review-schema' )
            );
        }

        if ( 'trash' === $the_comment_status ) {
            $actions['untrash'] = sprintf(
                '<a href="%s" data-wp-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                $untrash_url,
                "delete:the-comment-list:comment-{$comment->comment_ID}:66cc66:untrash=1",
                esc_attr__( 'Restore this comment from the Trash', 'review-schema' ),
                esc_html__( 'Restore' , 'review-schema')
            );
        }

        if ( 'spam' === $the_comment_status || 'trash' === $the_comment_status || ! EMPTY_TRASH_DAYS ) {
            $actions['delete'] = sprintf(
                '<a href="%s" data-wp-lists="%s" class="vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                $delete_url,
                "delete:the-comment-list:comment-{$comment->comment_ID}::delete=1",
                esc_attr__( 'Delete this comment permanently', 'review-schema' ),
                esc_html__( 'Delete Permanently', 'review-schema' )
            );
        } else {
            $actions['trash'] = sprintf(
                '<a href="%s" data-wp-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
                $trash_url,
                "delete:the-comment-list:comment-{$comment->comment_ID}::trash=1",
                esc_attr__( 'Move this comment to the Trash', 'review-schema' ),
                esc_html_x( 'Trash', 'verb', 'review-schema' )
            );
        } 

        if ( 'spam' !== $the_comment_status && 'trash' !== $the_comment_status ) {
            $edit_url = admin_url('admin.php?page=rtrs-reviews');
            $actions['edit'] = sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                $edit_url . '&action=edit&c=' . $item['id'],
                esc_attr__( 'Edit this comment', 'review-schema' ),
                esc_html__( 'Edit', 'review-schema' )
            );

            $format = '<button type="button" data-comment-id="%d" data-post-id="%d" data-action="%s" class="%s button-link" aria-expanded="false" aria-label="%s">%s</button>';

        }

        /** This filter is documented in wp-admin/includes/dashboard.php */
        $actions = apply_filters( 'review_row_actions', array_filter( $actions ), $comment );

		return $item['review'] . $this->row_actions( $actions );
	}    
	
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {  

			case 'review':
				return $item['review'];
				break; 

            case 'post_type':
                return ucfirst( get_post_type( $item['post_id'] ) );
                break;

            case 'rating':
                if ( $item[$column_name] ) {
                    return Functions::review_stars( $item[$column_name], true ) . '(' . $item[$column_name] . ')';
                } else {
                    return '';
                } 
                break;
			
			case 'status':
				return $item[$column_name] == 1 ? esc_html__( 'Approved', 'review-schema' ) : esc_html__( 'Unapproved', 'review-schema' );
                break;
                
            case 'info':
                $h = '';
                $h .= '<div class="rtrs-tooltip rtrs-tooltip-review-info"><i class="dashicons dashicons-info"><span class="rtrs-tooltiptext rtrs-review-info">';
                $h .= sprintf(
                        '<table>
                            <tr><th>%1$s</th><td>%2$s</td></tr>
                            <tr><th>%3$s</th><td><a href="%4$s">%5$s</a></td></tr>
                            <tr><th>%6$s</th><td>%7$s</td></tr>
                        </table>',

                        esc_html__( 'Title:', 'review-schema' ),
                        esc_html( $item['title'] ),
                        
                        esc_html__( 'URL:', 'review-schema' ),
                        get_edit_post_link( $item['post_id'] ), 
                        get_the_title( $item['post_id'] ),

                        esc_html__( 'Author:', 'review-schema' ),
                        esc_html( $item['author'] )
                    );
                $h .= '</span></i></div>';
                return $h;
                
                break;
    

			case 'date':
                return $item[$column_name];
                break;

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
     * Displays the comments table.
     *
     * Overrides the parent display() method to render extra comments.
     *
     * @since 1.0.0
     */
    public function display() {
        $singular = $this->_args['singular'];
 
        $this->display_tablenav( 'top' );
 
        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>
			<?php //Todo: the-comment-list add to make it ajax and style need to check tr class id in talbe like comment ?>
			<tbody id="dthe-comment-list" data-wp-lists="list:comment">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		
			<tfoot>
                <tr>
                    <?php $this->print_column_headers( false ); ?>
                </tr>
			</tfoot> 
		</table>
        <?php
        $this->display_tablenav( 'bottom' );
    }

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'reviews_per_page', 5 );
        $current_page = $this->get_pagenum();
        
		$total_items  = $this->review_data()['total_reviews_count'];

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = $this->review_data( $current_page )['all_reviews'];  
	}

	/**
     * @return array
     */
    public function review_data( $current_page = null ) {
         
        $post_types   = Functions::allReviewType();   
        $type         = array( 'review' );

		$per_page     = $this->get_items_per_page( 'reviews_per_page', 5 );
		$current_page = $this->get_pagenum();

		$search     = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
        $orderby    = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : '';
        $order      = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : ''; 
		$comment_status = isset( $_GET['comment_status'] ) ? sanitize_text_field( $_GET['comment_status'] ) : 'all';
		
		switch ( $comment_status ) {

			case 'moderated':
				$comment_status = 'hold';
				break;

			case 'approved':
				$comment_status = 'approve';
				break;

            case 'all':
            case 'mine':
				$comment_status = '';
				break; 
		}

        $args = [ 
            'status'    => $comment_status,
            'search'    => $search, 
            'post_type' => $post_types,
            'type'  	=> $type,
            'number'    => $per_page,
        ];

        if ( $orderby == 'rating' ) {
            $args['meta_key'] = $orderby;
            $args['orderby'] = 'meta_value_num';
            $args['order']   = $order;
        } else {
            $args['orderby'] = $orderby;
            $args['order']   = $order;
        } 

		if ( $current_page > 1 ) {
			$args['offset'] = ( $current_page * $per_page ) - $per_page;
		}

        $get_review_info = $all_comments = [];

        $comments = get_comments($args);

        foreach ( $comments as $comment ) { 
            $ratings = [];
 
			$date = sprintf( 
				esc_html__( '%1$s at %2$s', 'review-schema' ), 
				get_comment_date( get_option( 'date_format' ), $comment ), 
				get_comment_date( get_option( 'time_format' ), $comment )
			);

            $data = [
                'id'               => $comment->comment_ID,
                'post_id'		   => $comment->comment_post_ID,
                'parent_id'		   => $comment->comment_parent,
                'author'	       => $comment->comment_author,
                'status'           => $comment->comment_approved, 
                'title'  		   => get_comment_meta( $comment->comment_ID, 'rt_title', true ),
                'review'  		   => $comment->comment_content,
                'date'      	   => $date,
                'rating'     	   => get_comment_meta( $comment->comment_ID, 'rating', true ),
            ];

            $all_comments[] = $data;
        }

        $total_comments_count = get_comments(
            array_merge( $args, ['count'  => true,'offset' => 0,'number' => 0] )
        );

        $get_review_info['all_reviews']         = $all_comments; 
        $get_review_info['total_reviews_count'] = $total_comments_count;

        return $get_review_info;
    }

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
    public function get_bulk_actions() {
        // global $comment_status; 
		// Todo: global comment_status not working that's why doing it manually 
		$comment_status = isset( $_GET['comment_status'] ) ? sanitize_text_field( $_GET['comment_status'] ) : 'all';

        $actions = array();
        if ( in_array( $comment_status, array( 'all', 'approved' ) ) ) {
            $actions['unapprove'] = esc_html__( 'Unapprove', 'review-schema' );
        }
        if ( in_array( $comment_status, array( 'all', 'moderated' ) ) ) {
            $actions['approve'] = esc_html__( 'Approve', 'review-schema' );
        }
        if ( in_array( $comment_status, array( 'all', 'moderated', 'approved', 'trash' ) ) ) {
            $actions['spam'] = esc_html_x( 'Mark as Spam', 'comment', 'review-schema' );
        }

        if ( 'trash' === $comment_status ) {
            $actions['untrash'] = esc_html__( 'Restore', 'review-schema');
        } elseif ( 'spam' === $comment_status ) {
            $actions['unspam'] = esc_html_x( 'Not Spam', 'comment', 'review-schema' );
        }

        if ( in_array( $comment_status, array( 'trash', 'spam' ) ) || ! EMPTY_TRASH_DAYS ) {
            $actions['delete'] = esc_html__( 'Delete Permanently', 'review-schema' );
        } else {
            $actions['trash'] = esc_html__( 'Move to Trash', 'review-schema' );
        }

        return $actions; 
    }  
	 
	/**
     * Process Bulk Action
     *
     * @return void
     */
    public function process_bulk_action() {
		
		// security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) { 
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) ) { 
				wp_die( esc_html__('Action failed. Please refresh the page and retry.', 'review-schema') );
			}   
        }

        $doaction = $this->current_action();
        if ( $doaction ) { 
			if ( !isset( $_REQUEST['delete_reviews'] ) ) return;
            $comment_ids = array_map( 'absint', $_REQUEST['delete_reviews'] );

            $approved = $unapproved = $spammed = $unspammed = $trashed = $untrashed = $deleted = 0;

            $redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids' ), wp_get_referer() );

            wp_defer_comment_counting( true );

            foreach ( $comment_ids as $comment_id ) { // Check the permissions on each.
                if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
                    continue;
                } 

                switch ( $doaction ) {
                    case 'approve':
						
                        wp_set_comment_status( $comment_id, 'approve' );
                        $approved++;
                        break;

                    case 'unapprove':
                        wp_set_comment_status( $comment_id, 'hold' );
                        $unapproved++;
                        break;

                    case 'spam':
                        wp_spam_comment( $comment_id );
                        $spammed++;
                        break;

                    case 'unspam':
                        wp_unspam_comment( $comment_id );
                        $unspammed++;
                        break;

                    case 'trash':
                        wp_trash_comment( $comment_id );
                        $trashed++;
                        break;

                    case 'untrash':
                        wp_untrash_comment( $comment_id );
                        $untrashed++;
                        break;

                    case 'delete': 
                        if ( $get_attachment = get_comment_meta( $comment_id, 'rt_attachment', true ) ) {    
                            if ( isset( $get_attachment['imgs'] ) ) {  
                                foreach( $get_attachment['imgs'] as $attachment_id) {   
                                    wp_delete_attachment( $attachment_id );
                                } 
                            }  
                             
                            if ( isset( $get_attachment['videos'] ) ) {  
                                foreach( $get_attachment['videos'] as $attachment_id ) {  
                                    $self_video = ( isset( $get_attachment['video_source'] ) && $get_attachment['video_source'] == 'self' ); 
                                    if ( $self_video ) {
                                        wp_delete_attachment( $attachment_id );
                                    }  
                                } 
                            } 
                        }
                                
                        wp_delete_comment( $comment_id );
                        $deleted++;
                        break;
                }
            }

            if ( ! in_array( $doaction, array( 'approve', 'unapprove', 'spam', 'unspam', 'trash', 'delete' ), true ) ) {
                $screen = get_current_screen()->id;

                /** This action is documented in wp-admin/edit.php */
                $redirect_to = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $doaction, $comment_ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
            }

            wp_defer_comment_counting( false );

            if ( $approved ) {
                $redirect_to = add_query_arg( 'approved', $approved, $redirect_to );
            }
            if ( $unapproved ) {
                $redirect_to = add_query_arg( 'unapproved', $unapproved, $redirect_to );
            }
            if ( $spammed ) {
                $redirect_to = add_query_arg( 'spammed', $spammed, $redirect_to );
            }
            if ( $unspammed ) {
                $redirect_to = add_query_arg( 'unspammed', $unspammed, $redirect_to );
            }
            if ( $trashed ) {
                $redirect_to = add_query_arg( 'trashed', $trashed, $redirect_to );
            }
            if ( $untrashed ) {
                $redirect_to = add_query_arg( 'untrashed', $untrashed, $redirect_to );
            }
            if ( $deleted ) {
                $redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
            }
            if ( $trashed || $spammed ) {
                $redirect_to = add_query_arg( 'ids', join( ',', $comment_ids ), $redirect_to );
            }
        } 
    }  
} 