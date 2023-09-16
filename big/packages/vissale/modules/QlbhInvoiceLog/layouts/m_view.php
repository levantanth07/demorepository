<div class="row">
  <div class="col-xs-10 col-sm-10">
  </div>
  <div class="col-xs-2 col-sm-2" style="padding-top:23px;">
    <a href="<?php echo Url::build_current(array('type'));?>"  class="btn btn-default">DS PX</a>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-xs-12 col-sm-12">
    <div style="width:100%;padding:10px;text-align:center;font-size:14px;float:left;">	
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left">
          <strong><?php echo Portal::get_setting('company_name');?></strong><br />
          ĐC: <?php echo Portal::get_setting('company_address');?><BR>
          Tel: <?php echo Portal::get_setting('company_phone');?> | Fax:<?php echo Portal::get_setting('company_fax');?><br />
          Email: <?php echo Portal::get_setting('email');?> | Website:<?php echo Portal::get_setting('website');?>
        </td>
        <td align="right">
          S&#7889;: [[|bill_number|]]<br />
          Ng&#224;y:&nbsp;[[|day|]]/[[|month|]]/[[|year|]]
        </td>
      </tr>
    </table>
    </div><br clear="all">
    <div style="text-align:left;">
      <div style="width:99%;padding:2px 2px 2px 2px;text-align:center;font-size:14px;">
        <div style="padding:2px 2px 2px 2px;">
        <div style="text-indent:0px;vertical-align:top;font-size:16px;text-transform:uppercase;font-weight:bold;">[[|title|]]</div>
        <div>
          <table width="100%">
            <tr valign="top">
              <td width="70%" style="font-size:12px;text-align:left">
                &#272;&#417;n v&#7883;:
                <!--IF:cond(Url::get('type')=='IMPORT' or [[=supplier_id=]])-->
                [[|supplier_name|]]<br>
                   <!--ELSE-->
                [[|warehouse_name|]] <br>
                <!--/IF:cond-->
                  Ng&#432;&#7901;i giao: [[|deliver_name|]] <br />
                Ng&#432;&#7901;i nh&#7853;n: [[|receiver_name|]						</td>
              <td width="30%" align="right" nowrap="nowrap"  style="font-size:12px;">Nh&acirc;n vi&ecirc;n: [[|staff_name|]]<br /></td>
            </tr>
            <tr valign="top">
              <td style="font-size:12px;text-align:left">Di&#7877;n gi&#7843;i: 
                <!--IF:cond([[=note=]])--><em>[[|note|]]</em><!--ELSE-->...<!--/IF:cond--></td>
              <td align="right" nowrap="nowrap"  style="font-size:12px;">&nbsp;</td>
            </tr>
          </table>
          </div>
        <div style="padding:2px 2px 2px 2px;text-align:left;">
          &nbsp;
        </div>
          <div style="text-align:left;">
          <table width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse" bordercolor="#000000">
            <tr>
            <th width="4%" scope="col">STT</th>
            <th width="25%" align="center" scope="col">T&ecirc;n SP, HH <br /></th>
            <th width="11%" align="center" scope="col">M&atilde; s&#7889; </th>
            <th width="10%" scope="col" align="center">&#272;&#417;n v&#7883;</th>
            <th width="15%" align="center" scope="col">Kho</th>
            <th width="10%" scope="col" align="center">s&#7889; l&#432;&#7907;ng  </th>
            <th width="100" scope="col" align="center">&#272;&#417;n gi&aacute; </th>
            <th width="160" scope="col" align="center">Th&agrave;nh ti&#7873;n </th>
            </tr>
            <tr>
            <td align="center">A</td>
            <td align="center">B</td>
            <td align="center">C</td>
            <td align="center">D</td>
            <td align="center">E</td>
            <td align="center">1</td>
            <td width="150" align="center">2</td>
            <td align="right" nowrap="nowrap">3=(1x2)</td>
            </tr>
            <!--LIST:products-->
            <tr>
            <td align="center">[[|products.i|]]</td>
            <td align="left" style="padding:0 0 0 10px;">[[|products.name|]]</td>
            <td align="center" nowrap="nowrap">[[|products.product_code|]]</td>
            <td align="center">[[|products.unit_name|]]</td>
            <td align="left">[[|products.warehouse|]]</td>
            <td align="right">[[|products.number|]]</td>
            <td width="150" align="right">[[|products.price|]]</td>
            <td width="150" align="right">[[|products.payment_amount|]]</td>
            </tr>
              <!--/LIST:products-->
            <?php for($i=0;$i<=20;$i++){?><tr>
              <td>&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="center">&nbsp;</td>
              <td align="right">&nbsp;</td>
              </tr>
            <?php 
            if($i==1)
            {
              echo '<div style="display:none;page-break-after:always;">';
            }
            }?><tr>
            <td>&nbsp;</td>
            <td align="center">T&#7893;ng c&#7897;ng </td>
            <td align="center">x</td>
            <td align="center">x</td>
            <td align="center">x</td>
            <td align="center">x</td>
            <td align="center">x</td>
            <td  align="right">[[|total|]]</td>
            </tr>
          </table>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td colspan="4" align="right">T&#7893;ng s&#7889; ti&#7873;n(Vi&#7871;t b&#7857;ng ch&#7919;):&nbsp;<em>[[|total_by_letter|]]</em> 
                    <br /></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td colspan="2" align="right"><em>Ng&#224;y&nbsp;[[|day|]]&nbsp;th&#225;ng&nbsp;[[|month|]]&nbsp;n&#259;m&nbsp;[[|year|]]&nbsp;</em></td>
          </tr>
          <tr>
            <td align="center" width="25%">Th&#7911; tr&#432;&#7903;ng &#273;&#417;n v&#7883;<br />
                  <em>(K&#253;, h&#7885; t&#234;n)</em></td>
            <td width="25%" align="center">Ng&#432;&#7901;i giao h&agrave;ng<br />
                  <em>(K&#253;, h&#7885; t&#234;n)</em></td>
            <td width="25%" align="center"><span style="width:25%;text-align:center;">Th&#7911; kho<br />
                        <em>(K&#253;, h&#7885; t&#234;n)</em></span></td>
            <td width="25%" align="center"><span style="width:25%;text-align:center;">Ng&#432;&#7901;i nh&#7853;n h&agrave;ng<br />
                        <em>(K&#253;, h&#7885; t&#234;n)</em></span></td>
          </tr>
        </table>
    
      </div>
    </div>
</div>