<?php
/**
 * Plugin Name: Wp Food Menu
 * Plugin URI: https://www.linkedin.com/in/rashmi-sonke-65838bb6/
 * Description: This plugin adds some Facebook Open Graph tags to our single posts.
 * Version: 1.0.0
 * Author: Rashmi Sonke
 * Author URI: https://www.linkedin.com/in/rashmi-sonke-65838bb6/
 * License: GPL2
 */

include("function.php");
ob_start();
global $jal_db_version;
$jal_db_version = '1.0';


function fdcat_install() {
    global $wpdb;
    global $jal_db_version;

    $table_n = $wpdb->prefix . 'food_menu_cat_table';
    
    $charset_collatee = $wpdb->get_charset_collate();

    $sqll = "CREATE TABLE $table_n (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        cat_name  varchar(255) DEFAULT '' NOT NULL,
        cat_image  varchar(255) DEFAULT '' NOT NULL,
       
        UNIQUE KEY id (id)
    ) $charset_collatee;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sqll );

    add_option( 'jal_db_version', $jal_db_version );
}



function fd_install() {
    global $wpdb;
    global $jal_db_version;

    $table_name = $wpdb->prefix . 'food_menu_items';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        cat_id mediumint(9) NOT NULL,
        item_name  varchar(255) DEFAULT '' NOT NULL,
        eng_detail  text NOT NULL,
        sv_detail   text NOT NULL,
        item_price  varchar(255) DEFAULT '' NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'jal_db_version', $jal_db_version );
}

register_activation_hook( __FILE__, 'fdcat_install' );
register_activation_hook( __FILE__, 'fd_install' );


add_action("admin_menu","wpfoodmenu_admin_actions");
function wpfoodmenu_admin_actions()
{
add_menu_page('Wp Food Menu','Wp Food Menu','manage_options','wp-food-menu','wpfoodmenu_admin');
add_submenu_page('wp-food-menu', __('Add Category'), __('Add Category'), 'manage_options', 'add_category', 'add_categoryfun');

add_submenu_page('wp-food-menu',__('View Menu Item'), __('View Menu Item'), 'manage_options', 'view_menu_items', 
    'view_menu_itemsfu');

add_submenu_page('wp-food-menu',__('Add Menu Item'), __('Add Menu Item'), 'manage_options', 'add_new_menu_items', 
    'add_new_menu_itemsfu');

}

/* for wp-table
$Paulund_Wp_List_Tablee = new Paulund_Wp_List_Tablee();
   $Paulund_Wp_List_Tablee->list_table_page();*/
function add_categoryfun()
{
if(isset($_POST['save'])){
savecat();
}
?>
<div style="width: 80%;border:1px solid;border-color: lightgrey;">
<h1>Add New Menu Category.<a href="admin.php?page=wp-food-menu">View</a></h1>
<form action='' method="post" enctype="multipart/form-data">
Category Name:  <input type="text" name="cat" id="cat" required="" /><br/>
upload image:   <input type="file" name="cfile" required="" /><br/>
<input type="submit" name="save" value="save"/>
</form>
</div>
<?php 
}

function add_new_menu_itemsfu()
{
if(isset($_POST['saveitem']))
    {
    saveitem();
    }
?>
<div style="width: 80%;border:1px solid;border-color: lightgrey;">
<h1>Add New Menu Item. <a href="admin.php?page=view_menu_items">View</a></h1>
<form action='' method="post" >
<table>
    <tr>
        <th>Category:</th>
         <td><select name="catid">
             <option value="0">--select--</option>
             <?php global $wpdb;
$tb = $wpdb->prefix . 'food_menu_cat_table';
$mylink = $wpdb->get_results( "SELECT * FROM $tb ", ARRAY_A );
 //$pageposts = $wpdb->get_results($querystr, OBJECT);
 foreach ( $mylink as $key => $value) {
             ?>
              <option value="<?php echo $value['id']; ?>"><?php echo $value['cat_name']; ?></option>
              <?php }?>
         </select></td>
    </tr>
    <tr>
        <th>Name:</th>
         <td>
             <input type="text" name="itemname" id="itemname"/>
         </td>
    </tr>
    <tr>
        <th> Detail In English:</th>
         <td>
             <textarea name="detaileg" id="detaileg"></textarea>
         </td>
    </tr>
    <tr>
        <th> Detail In Swedish:</th>
         <td>
             <textarea name="detailsv" id="detailsv"></textarea>
         </td>
    </tr>
     <tr>
        <th>Price:</th>
         <td>
             <input type="text" name="price" id="price"/>
         </td>
    </tr>
    <tr><td colspan="2">
            <input type="submit" name="saveitem" value="save"/>
         </td>
    </tr>

</table>
 <br/>
</form>
</div>
<?php 
}

function wpfoodmenu_admin()
{ 
    if($_REQUEST['editcat']=='Update' )
    {
        update_cat($_GET['id']);
    }
    elseif($_GET['action']=='edit')
    {
        editcat($_GET['id'],$_GET['msg']);
    }
    
    elseif($_GET["action"]=="del")
    {
        global $wpdb;
        

$tbd = $wpdb->prefix . 'food_menu_cat_table';

$resultt=$wpdb->query("DELETE FROM $tbd WHERE id = '".$_GET['id']."'");
header("Location:admin.php?page=wp-food-menu&msg=1");   
    }
    else{

global $wpdb;
$customPagHTML     = "";
$tbs = $wpdb->prefix . 'food_menu_cat_table';
$query             = "SELECT * FROM $tbs";
$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total             = $wpdb->get_var( $total_query );
$items_per_page = 3;
$page             = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
$offset         = ( $page * $items_per_page ) - $items_per_page;
$result         = $wpdb->get_results( $query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}" );
$totalPage         = ceil($total / $items_per_page);
       
if($totalPage > 1){
$customPagHTML     =  '<div><span>Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
'base' => add_query_arg( 'cpage', '%#%' ),
'format' => '',
'prev_text' => __('&laquo;'),
'next_text' => __('&raquo;'),
'total' => $totalPage,
'current' => $page
)).'</div>';
}
?>
<p style="font-size: 16px;font-weight: bold;"><?php if($_GET['msg']==1){ echo "Record Deleted.";}?><p>
<h1>Category <a class="page-title-action" href="?page=add_category">Add New</a></h1>
<table class="wp-list-table widefat fixed striped pages">
    <tr>

    <th class="manage-column" >Image</th>
    <th class="manage-column" >Title</th>
    <th class="manage-column" >Action</th>
    </tr>
<?php 
if(count($result)>0){

foreach ($result as $key => $val) { 
?>
<tr class="iedit type-page status-publish hentry" style="color: #ddd;">

<td>
<img src="<?php echo site_url()."/wp-content/". $result[$key]->cat_image; ?>" width="80px" heigh="80px"/>
</td>
<td>
<strong>
<a  href="?page=wp-food-menu&id=<?php echo $result[$key]->id; ?>&amp;action=edit" class="row-title"><?php echo $result[$key]->cat_name; ?></a></strong>
</td>  
<td>
<strong>
<a  href="?page=wp-food-menu&id=<?php echo $result[$key]->id; ?>&amp;action=del" class="row-title">delete</a></strong>
</td>  
</tr>
<?php 
} 
}else{ echo '<tr><td colspan="3" style="font-size:16px;text-align:center;">
<strong>No Record Found.</strong></td></tr>' ;}
} ?>
</table>
<?php 
echo $customPagHTML;
}

function view_menu_itemsfu()
{
    
 if($_POST['edititem']=='Update' )
    {
  update_item($_GET['mid']);
    }
  elseif($_GET['action']=='edit')
    {
editmenu($_GET['mid'],$_GET['msg']);
    }
elseif($_GET["action"]=="delm")
    {
global $wpdb;        
$delm=$_GET['mid'];
$tbmd = $wpdb->prefix . 'food_menu_items';
$resultt=$wpdb->query("DELETE FROM $tbmd WHERE id = $delm");
header("Location:admin.php?page=view_menu_items&msg=1");   
    }
     else{
        ?>
       <p style="font-size: 16px;font-weight: bold;"><?php if($_GET['msg']==1){ echo "Record Deleted.";}?><p>
        <h1>Menu Items <a class="page-title-action" href="?page=add_new_menu_items">Add New</a></h1>
<div style="float: right;">
<form action="admin.php?page=view_menu_items" method="post">

<input type="text" name="srchiname" placeholder="Enter Item Name" />
<input type="submit" value="search"/>
</form>
</div><?php 
global $wpdb;
if($_POST['srchiname']!="")
{
$str='WHERE item_name like "%'.mysql_escape_string($_POST['srchiname']).'%" '; 
}
else{
    $str="";
}
$customPagHTML     = "";

$tbml = $wpdb->prefix . 'food_menu_items';
 $query             = "SELECT * FROM $tbml $str ";
  
$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total             = $wpdb->get_var( $total_query );
$items_per_page = 5;
$page             = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
$offset         = ( $page * $items_per_page ) - $items_per_page;
$result         = $wpdb->get_results( $query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}" );
$totalPage         = ceil($total / $items_per_page);
       
if($totalPage > 1){
$customPagHTML     =  '<div><span>Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
'base' => add_query_arg( 'cpage', '%#%' ),
'format' => '',
'prev_text' => __('&laquo;'),
'next_text' => __('&raquo;'),
'total' => $totalPage,
'current' => $page
)).'</div>';
}
?>
<table class="wp-list-table widefat fixed striped pages">
<tr style="background-color: lightgrey;">
    <th class="manage-column" ><strong>Item Name</strong></th>
    <th class="manage-column" ><strong>Category</strong></th>
    <th class="manage-column" ><strong>ENG Detail</strong></th>
    <th class="manage-column" ><strong>SV Detail</strong></th>
    <th class="manage-column" ><strong>Item Price</strong></th>
    <th class="manage-column" ><strong>Action</strong></th>
</tr>
<?php 
if(count($result)>0)
{
foreach ($result as $key => $val) { 
?>
<tr class="iedit type-page status-publish hentry" style="color: #ddd;">      
<td>
<strong>
<a  href="?page=view_menu_items&mid=<?php echo $result[$key]->id; ?>&amp;action=edit" class="row-title"><?php echo $result[$key]->item_name; ?></a></strong>
</td>
<td>
<strong>
<?php 
$cc=$result[$key]->cat_id; 
$tbss = $wpdb->prefix . 'food_menu_cat_table';
$qry=$wpdb->get_results("select * from $tbss where id='$cc'");
?>
<img src="<?php echo site_url()."/wp-content/".$qry['0']->cat_image;?>" title="<?php echo $qry['0']->cat_name;?>" width="60px" height="60px">
</strong></td>
</td>
<td>
<strong>
<?php if($result[$key]->eng_detail!=""){?>
<a href="?page=view_menu_items&mid=<?php echo $result[$key]->id; ?>&amp;action=edit" class="row-title"><?php echo $result[$key]->eng_detail; ?></a><?php } else{echo "N/A";}?></strong>
</td> 
<td>

<strong>
<?php if($result[$key]->sv_detail!=""){?>
<a href="?page=view_menu_items&mid=<?php echo $result[$key]->id; ?>&amp;action=edit" class="row-title"><?php echo $result[$key]->sv_detail; ?></a>
<?php } else{echo "N/A";}?></strong>
</td> 
<td>
<strong>
<a href="?page=view_menu_items&mid=<?php echo $result[$key]->id; ?>&amp;action=edit" class="row-title"><?php echo $result[$key]->item_price; ?></a></strong>
</td> 
<td>

<a href="?page=view_menu_items&mid=<?php echo $result[$key]->id; ?>&amp;action=delm" class="row-title">delete</a>
</td>
</tr>
<?php } }else{ echo '<tr><td colspan="6" style="font-size:16px;text-align:center;">
<strong>No Record Found.</strong></td></tr>' ;}} ?>
</table>
<?php 
echo $customPagHTML;
}
function view_menu_items()
{
?>
<style>
.container{

    background-color: #00666a !important;
}

.left-col{

        padding-left: 25px !important;
        padding-top: 7px !important;
}

.line1{
    margin-left: 45px;
}

.line2{

    margin-right: 95px;
}

.eng-row{

    clear: both;
}

.contain-body{
  width: 100%;
}


.title-font:focus{
    color: #ffffff !important;
    text-decoration: none !important;
}

.left img{
    border: 8px solid #eee;
    box-shadow: 1px 1px 4px #ccc;

    }

.title{

    position: relative;
    cursor: pointer;
    min-height: 20px;
    font-weight: bold;
    font-size: 32px;
    color: white;
        padding-top: 10px;   
}

.left-title{

  padding-left: 47px;
}

.title-font:hover {
    color: #ffffff !important;
    text-decoration: none !important;
}


.title-font{
    color: #ffffff;
    text-decoration: none !important;
    font-size: 28px;
    font-weight: bold;
    box-shadow: none !important; 

}

.title-panel{

    text-transform: capitalize;
}

.gly-font:focus{
  color:#ffffff;
  text-decoration: none;
}

.gly-font{
    width: 20px;
    height: 20px;
    text-align: center;
    font-size: 14px;
   
 margin-right: 5px;
}
strong{

    font-size: 16px;
   }

    .price{
font-size: 16px;
float: right;
   
    }


   .inner-content{

  color:#ffffff;
  width: 90%;
  margin-left: 10%;
 
}
    

    .inline-content{
    line-height: 40px;
    font-size: 16px;
    }

    .small-content{
        font-size: 16px;
    }

    .left-heading{
     width:60%;
     float:left;
     font-size:16px;
    }

    .right-price{
     width:40%;
     float:right;
     text-align: right;

    }


    .left {
    width: 100%;
  } 

  .outer{

        margin-left: 10%;
    margin-top: 20px;
  }

 .main-row{
      margin-right: 15px !important;
    margin-left: 15px !important;
 }
.entry-content{

    margin-left: 0 !important;
}

.right-all{

    padding-left: 25px;
    padding-top: 7px !important;

}

.inner-right{
    margin-left: 0% !important;
    color:#ffffff;
}

.right-outer{
margin-top: 20px;
   

}



@media screen and (max-width: 768px) {
  .left img{
    width: 100%;
  } 
  .line1{
    margin-left: 0px;
}

 .line2{
    margin-right: 0px;
}
}

@media screen and (max-width: 768px) {
  .outer {
    margin-left: 0;

}

.left-title {
    padding-left: 0;
}

 .line1{
    margin-left: 0px;
}
.line2{
    margin-right: 0px;
}

}







</style>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>




<div class="container">
<div class="row main-row">


<!--left col start here-->
<div class="col-md-6">
<?php 
    global $wpdb;
    $tbf = $wpdb->prefix . 'food_menu_cat_table';
    $qry=$wpdb->get_results("SELECT cat_name,cat_image,id FROM $tbf WHERE 1");
     $num=count($qry);
   $st =ceil($num/2);

   
for($i=0;$i<$st;$i++) 
{ 

    ?>
<div class="col-inner left-col">

<div class="accordion-group" id="accordion">
<div class="accordion-panel" >
        <div class="heading glyphicon-custom">
            <h4 class="title left-title">
                <a class="accordion-toggle title-font" data-toggle="collapse" onclick="acc(<?php echo $i; ?>);" data-parent="#accordion" href="#panel<?php echo $i; ?>"><i class="glyphicon glyphicon-plus gly-font  "></i><span class="title-panel"> 
                <?php echo $qry[$i]->cat_name; $ccid= $qry[$i]->id ;?></span></a>
            </h4>
        </div>


<div id="panel<?php echo $i; ?>" class="panel-collapse collapse">
    <div class="contain-body"> 
    <hr class="line1" style="border-top: dotted 1px;">

    <?php
$tbmf = $wpdb->prefix . 'food_menu_items';

    $qryy=$wpdb->get_results("SELECT `item_name`,`eng_detail`,`sv_detail`,`item_price` FROM $tbmf WHERE `cat_id`=$ccid");
foreach ($qryy as $keyy => $valu) 
{
  ?>  
    <div class="su-spoiler-content su-clearfix inner-content">
<div class="small-content" style="width:100%;">
<div class="inline-content" style="width:100%;">
<div class="left-heading">
<strong class="item"><?php echo $qryy[$keyy]->item_name; ?> </strong>
</div>

<div  class="right-price">
  <?php echo $qryy[$keyy]->item_price;?>
</div>
</div>
<?php if($qryy[$keyy]->eng_detail!=""){
    ?>


<div class="eng-row">
<p><?php echo $qryy[$keyy]->eng_detail; ?></p>
</div>

<?php }?>
 <?php if($qryy[$keyy]->sv_detail!=""){
    ?>


<p><em><?php echo $qryy[$keyy]->sv_detail; ?>
</em></p>

<?php }?>

</div>

 </div>
<?php }?>
    </div>

</div>
</div>
</div>


<div class="left">


 <!-- left-image  -->
  <div class="outer" >
    <img src="<?php echo site_url()."/wp-content/".$qry[$i]->cat_image;?>" width="400" height="267">
  </div>
</div>


</div>
<?php }?>
   </div>
<!-- left-columnend  -->


<!--right-columnstart  -->

<div class="col-md-6 ">
<?php for($j=$st;$j<$num;$j++) 
{ 

    ?>
<div class="col-inner right-all"><div class="col-inner"><div class="accordion-group" id="accordion"><div class="accordion-panel" >
        <div class="heading glyphicon-custom">
            <h4 class="title">
                <a class="accordion-toggle title-font" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $j; ?>"><i class="glyphicon glyphicon-plus gly-font  " onclick="acc(<?php echo $j; ?>);"></i><span class="title-panel"><?php echo $qry[$j]->cat_name; $ccid= $qry[$j]->id ;?></span></a>
            </h4>
        </div>


        <div id="panel<?php echo $j; ?>" class="panel-collapse collapse">
             <div class="contain-body"> 
             <hr class="line2" style="border-top: dotted 1px;"> 
             <?php 
             $tbmb = $wpdb->prefix . 'food_menu_items';
    $qryy=$wpdb->get_results("SELECT `item_name`,`eng_detail`,`sv_detail`,`item_price` FROM $tbmb WHERE `cat_id`=$ccid");
foreach ($qryy as $keyy => $valu) 
{
  ?>    
   <div class="su-spoiler-content su-clearfix inner-right">


<div class="small-content" style="width:100%;">
<div class="inline-content" style="width:100%;">
<div class="left-heading">
<strong class="item"> <?php echo $qryy[$keyy]->item_name; ?></strong>
</div>

<div  class="right-price">
  <?php echo $qryy[$keyy]->item_price;?>
</div>
</div>
<?php if($qryy[$keyy]->eng_detail!=""){
    ?>
<p><?php echo $qryy[$keyy]->eng_detail; ?></p>
<?php }?>
 <?php if($qryy[$keyy]->sv_detail!=""){
    ?>
<p><em><?php echo $qryy[$keyy]->sv_detail; ?>
</em></p><?php }?>

</div>



<br/>

</div>
<?php }?>
            </div>
        </div>



    </div>
</div>

 <!-- right-image  -->
 <div class="left">
<!-- left-image  -->
  <div class="right-outer" >
    <img src="<?php echo site_url()."/wp-content/".$qry[$j]->cat_image;?>" width="400" height="267">
  </div>
</div>
</div>


</div>
 
 <?php }?>

  <!-- right-col  -->

</div>
<!-- right-column end here -->
</div>

</div>




<script type="text/javascript">
function acc(selectid)
{

var selectIds =$('#panel'+selectid);
$(function ($) {

      selectIds.on('hidden.bs.collapse show.bs.collapse', function () {
      $(this).prev().find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
    })
});
}
</script>

<?php
}
add_shortcode( 'wp-food-menu', 'view_menu_items' );
?>

   


