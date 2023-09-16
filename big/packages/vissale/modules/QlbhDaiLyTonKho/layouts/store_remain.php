<link rel="stylesheet" href="skins/default/report.css">
<div class="row">
<div class="col-xs-12 col-sm-12">
<table width="100%" class="table">
	<tr>
		<td align="left">
			<button type="button" class="btn btn-default" onclick="window.location='index062019.php?page=qlbh_dai_ly_ton_kho'">Tùy chọn xem báo cáo</button>
		</td>
		<td align="right"><strong>[[.Warehouse.]]: [[|warehouse|]]</strong><br />
</td>
	</tr>
</table>
	<div style="width:100%;" >
		<div style="padding:2px;">
		<div class="report_title" align="center">[[|title|]]</div>
		<div>
			<table width="100%">
				<tr valign="top">
					<td style="font-size:12px;text-align:center;"><br />
						T&#7915; ng&agrave;y [[|date_from|]] &#273;&#7871;n ng&agrave;y [[|date_to|]]
					</td>
				</tr>
			</table>
	    </div>
		<div style="padding:2px 2px 2px 2px;text-align:left;">
			&nbsp;
		</div>
	    <div style="text-align:left;">
			<table width="100%" class="table table-bordered">
			  <tr>
				<th width="10%" align="left" scope="col">M&atilde; h&agrave;ng <br /></th>
				<th width="30%" align="left" scope="col">T&ecirc;n v&#7853;t t&#432; h&agrave;ng h&oacute;a </th>
				<th width="10%" align="center" scope="col">&#272;VT</th>
				<th align="center" scope="col">T&#7891;n &#273;&#7847;u k&#7923; </th>
				<th align="center" scope="col">Nh&#7853;p trong k&#7923; </th>
				<th align="center" scope="col">Xu&#7845;t trong k&#7923; </th>
				<th scope="col" align="center">T&#7891;n cu&#7889;i k&#7923; </th>
			  </tr>
				<?php $category = '';?>		
			  <!--LIST:products-->
				<?php if($category != [[=products.category_id=]] ){$category=[[=products.category_id=]];?>
				<tr>
					<td colspan="10" class="category-group">[[|products.category_id|]]</td>
				</tr>
				<?php }?>
			  <tr>
			    <td align="left">[[|products.product_code|]]</td>
			    <td align="left">[[|products.name|]]</td>
			    <td align="center">[[|products.unit|]]</td>
			    <td align="right"><?php echo System::display_number(round([[=products.start_term_quantity=]],2));?></td>
			    <td align="right"><?php echo System::display_number(round([[=products.import_number=]],2));?></td>
			    <td align="right"><?php echo System::display_number(round([[=products.export_number=]],2));?></td>
			    <td align="right"><?php echo System::display_number(round([[=products.remain_number=]],2));?></td>
		      </tr>
  			  <!--/LIST:products-->
		  </table>
		</div>
		</div>
	</div>
</div>
</div>
