<?php
class AdminProductPostDB{
  function get_posts(){
     $sql = '
      SELECT
        fb_pages.id,fb_pages.page_id,fb_pages.page_name
      FROM
        fb_pages
      WHERE
        fb_pages.group_id = '.Session::get('group_id').'
        AND fb_pages.status = 0
      ORDER BY
        fb_pages.page_name
      '
     ;
      return DB::fetch_all($sql);
  }
}
?>