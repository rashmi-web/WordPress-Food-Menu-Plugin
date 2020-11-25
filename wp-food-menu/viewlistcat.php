<?php

if(is_admin())
{
    new Paulund_Wp_List_Table_Cat();
}

/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class Paulund_Wp_List_Table_Cat
{
  

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
        $exampleListTable = new Example_List_Tablee();
        $exampleListTable->prepare_items();
        ?> <form method="post">
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2>Category List Page <a class="page-title-action" href="?page=add_category">Add New</a></h2>
                <?php 
 $exampleListTable->prepare_items(); 
  $exampleListTable->display(); 
                 ?>
            </div>
            </form>
        <?php
    }
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Example_List_Tablee extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
function column_title($item) {
$title=$item["cat_name"];
  $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&title=%s">Edit</a>',$_REQUEST['page'],'catedit',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&title=%s">Delete</a>',$_REQUEST['page'],'catdelete',$item['id']),
        );

  return sprintf('%1$s %2$s', $item['cat_name'], $this->row_actions($actions) );
}
function no_items() {
  _e( 'No Record found.' );
}
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 5;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id'          => 'ID',
            'cat_image'       => 'CAT IMAGE'
            ,
            'cat_name'       => 'CAT NAME'
        );

        return $columns;
    }

function get_bulk_actions() {
  $actions = array(
    'delete'    => 'Delete'
  );
  return $actions;
}
function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="book[]" value="%s" />', $item["id"]
        );    
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('cat_name' => array('cat_name', false));
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();

      
        global $wpdb;

$mylink = $wpdb->get_results( "SELECT * FROM `wp-food-menu-cat-table` " );
$i=1;
 foreach ( $mylink as $key => $value) {
$idd=$mylink[$key]->id;
 $tt=$mylink[$key]->cat_name;

  $mm='<img src='.$mylink[$key]->cat_image.' width="40px" heigh="40px"/>';
        $data[]= array(
                    'id'          =>$i,
                    'cat_image'       => $mm
                     ,
                    'cat_name'       => $tt
                    );
        $i++;
    }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'cat_image':
            case 'cat_name':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }


        $result = strcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }
}
?>