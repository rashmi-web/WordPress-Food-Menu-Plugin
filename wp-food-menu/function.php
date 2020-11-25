<?php 

function savecat()
{

global $wpdb;
$catn=$_POST['cat'];
$path_array  = wp_upload_dir();
$path = str_replace('\\', '/', $path_array['path']);
$target_path_sia = uniqid().$_FILES["cfile"]["name"];
$ex=explode("wp-content", $path);
$finalpath=$ex['1'];
$imgpath=$finalpath."/".$target_path_sia;

move_uploaded_file($_FILES["cfile"]["tmp_name"],$path. "/" . $target_path_sia);


  $tbi = $wpdb->prefix . 'food_menu_cat_table';
    $wpdb->insert( $tbi, 
    array( 
        'cat_name' => $catn, 
        'cat_image'  => $imgpath,
        
    ), 
    array( 
      '%s', //data type is string
      '%s'
      
    ) );  
echo "<p style='fon-size:20px;color:green;font-weight:bold;'>Record Saved Successfully.</p>";
}
function update_cat($id)
{
global $wpdb;
$catn=$_POST['cat'];

if ($_FILES['cfile']['size']!=0)
{
$path_array  = wp_upload_dir();
 $path = str_replace('\\', '/', $path_array['path']);

 $target_path_sia = uniqid().$_FILES["cfile"]["name"];

move_uploaded_file($_FILES["cfile"]["tmp_name"],$path. "/" . $target_path_sia);
$ex=explode("wp-content", $path);

$finalpath=$ex['1'];
     // echo "Stored in: " . $path. "/"  .$target_path_sia;
$imgpath=$finalpath."/".$target_path_sia;
 $tbu = $wpdb->prefix . 'food_menu_cat_table';
$resultt=$wpdb->query("UPDATE $tbu SET cat_name = '$catn' ,cat_image='$imgpath' WHERE id = $id");

header("Location:admin.php?page=wp-food-menu&id=$id&action=edit&msg=succ");
}
else{
    
$imgpath=$_POST['imgurl'];

 $tbuc = $wpdb->prefix . 'food_menu_cat_table';
$result=$wpdb->query("UPDATE $tbuc SET cat_name = '$catn' ,cat_image='$imgpath' WHERE id = $id");

header("Location:admin.php?page=wp-food-menu&id=$id&action=edit&msg=succ");
}

}

function saveitem()
{

 global $wpdb;
 $tbms = $wpdb->prefix . 'food_menu_items';
 
$re=$wpdb->query("insert into $tbms (`cat_id`, `item_name`, `eng_detail`, `sv_detail`, `item_price`) values('".$_POST['catid']."','".$_POST['itemname']."','".$_POST['detaileg']."','".$_POST['detailsv']."','".$_POST['price']."')");

  if($re==1)  {
echo "<p style='fon-size:20px;color:green;font-weight:bold;'>Record Saved Successfully.</p>";
}
}

function editcat($cid,$msg)
{
  
  global $wpdb; 
  $tbe = $wpdb->prefix . 'food_menu_cat_table';

  $querycat = $wpdb->get_results("SELECT * FROM $tbe where id='$cid'");


if($msg!=""){echo "<p style='fon-size:20px;color:green;font-weight:bold;'>Record Updated Successfully.</p>";}
echo "<h1>Edit category <a href='admin.php?page=wp-food-menu'>View</a></h1>";
?>
<div style="width: 80%;border:1px solid;border-color: lightgrey;">
<h1>Add New Menu Category.</h1>
<form action='' method="post" enctype="multipart/form-data">
Category Name:  <input type="text" name="cat" id="cat" required=""
 value="<?php echo $querycat['0']->cat_name;?>" /><br/>
upload image:   
<img src="<?php echo site_url()."/wp-content/". $querycat['0']->cat_image;?>"width="60px" heigh="60px" />
<input type="hidden" name="imgurl" value="<?php echo $querycat['0']->cat_image;?>"/>
<br/>
<input type="file" name="cfile"/><br/>
<input type="submit" name="editcat" value="Update"/>
</form>
</div>

<?php 
}

function editmenu($mid,$msg)
{




  global $wpdb; 

   $tbme = $wpdb->prefix . 'food_menu_items';
  $querym = $wpdb->get_results("SELECT * FROM $tbme where id='$mid'");
if($msg!=""){echo "<p style='fon-size:20px;color:green;font-weight:bold;'>Record Updated Successfully.</p>";}
echo "<h1>Edit Menu Item <a href='admin.php?page=view_menu_items'>View</a></h1>";
?>
<div style="width: 80%;border:1px solid;border-color: lightgrey;">
<form action='' method="post" >
<table>
    <tr>
        <th>Category:</th>
         <td><select name="catid">
             <option>--select--</option>
             <?php global $wpdb;
  $tbes = $wpdb->prefix . 'food_menu_cat_table';
$mylink = $wpdb->get_results( "SELECT * FROM $tbes ", ARRAY_A );
 //$pageposts = $wpdb->get_results($querystr, OBJECT);
 foreach ( $mylink as $key => $value) {
 ?>
<option value="<?php echo $value['id']; ?>" 
<?php if ($querym['0']->cat_id==$value['id'] ){echo "selected='selected'";}?> >
<?php echo $value['cat_name']; ?>
</option>
<?php }?>
</select>
</td>
    </tr>
    
<tr>
<th>Name:</th>
<td>
<input type="text" name="itemname" id="itemname" value=
"<?php echo $querym['0']->item_name;?>" />
</td>
</tr>
<tr>
<th> Detail In English:</th> 
<td>
<textarea name="eng_detail" id="eng_detail"><?php echo $querym['0']->eng_detail;?></textarea>
</td>
</tr>
<tr>
<th> Detail In Swedish:</th>
<td>
<textarea name="sv_dettail" id="sv_dettail">
<?php echo $querym['0']->sv_detail;?>
</textarea>
</td>
</tr>
<tr>
<th>Price:</th>
<td>
<input type="text" name="price" id="price" 
value="<?php echo $querym['0']->item_price;?>"/>
</td>
    </tr>
    <tr>
        
         <td colspan="2">
            <input type="submit" name="edititem" value="Update"/>
         </td>
    </tr>
</table>
 <br/>
</form>
</div>


<?php 



}



function update_item($mid)
{ global $wpdb;
  $tbmu = $wpdb->prefix . 'food_menu_items';

  
$resultt=$wpdb->query("UPDATE $tbmu 
    SET 
    cat_id = '".$_POST['catid']."',
    item_name = '".$_POST['itemname']."',
    eng_detail = '".$_POST['eng_detail']."',
    sv_detail = '".$_POST['sv_dettail']."',
    item_price = '".$_POST['price']."' 
    WHERE id = $mid");

header("Location:admin.php?page=view_menu_items&msg=succ");
}
?>