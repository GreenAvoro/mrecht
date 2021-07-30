<?php
function getCategories($root=true){
    $categories = get_categories([
        'taxonomy'  => 'product_cat',
        'hide_empty'=> false,
        'orderby'   => 'name'
        
    ]);
    //Only return root categories
    if($root){
        $root_categories = [];
        foreach($categories as $category){
            if($category->parent == 0){
                $root_categories[] = $category;
            }
        }
        return $root_categories;
    }else{
        return $categories;
    }
}