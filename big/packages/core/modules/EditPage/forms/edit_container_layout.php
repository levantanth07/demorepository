<?php
class EditContainerLayoutForm extends Form
{
	function EditContainerLayoutForm()
	{
		//khoi tao form
		Form::Form("editContainerLayout");
		$this->add('id',new IDType(true,false,'block'));
	}
	//ham thuc hien viec submit cua form
	function on_submit()
	{
		//thuc hien hanh dong xoa block
		if($this->check())
		{
			$block = new Block($_REQUEST['id'],Portal::$page);
			$block->set_setting('layout',$_REQUEST['layout']);
			Url::redirect('edit_page',array('id'=>$block->data['page_id'],'container_id'=>$block->data['id']));
		}
	}
	//ve form hien thi hanh dong them block moi
	function draw()
	{
		$block = new Block($_REQUEST['id'],Portal::$page);
		$this->parse_layout('edit_container_layout',
			array(
			'layout'=>$block->get_setting('layout','[[|center|]]')
			));
	}
}//end class
?>