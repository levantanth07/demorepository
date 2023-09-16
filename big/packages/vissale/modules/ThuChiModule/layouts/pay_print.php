<style>
    .printBox * {
        color: \#000;
    }
    table {
        page-break-inside: auto
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto
    }

    .fs18 {
        font-size: 18px;
    }

    .mb25 {
        margin-bottom: 25px;
    }

    .mb10 {
        margin-bottom: 10px;
    }

    .mb5 {
        margin-bottom: 5px;
    }
</style>
<div id="printBox" class="printBox">
    <div class="mb25">
        <span style="font-size:small;">{Chi_Nhanh_Ban_Hang}</span><br />
        <span style="font-size:small;">SĐT: </span><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;">{Dien_Thoai_Chi_Nhanh}</span><br />
        <span style="font-size:small;">Địa chỉ:&nbsp;{Dia_Chi_Chi_Nhanh}</span>
    </div><div class="mb10" style="text-align:center;"><strong class="fs18"><span style="font-size:large;">PHIẾU CHI</span></strong></div><div class="mb5" style="text-align:center;"><strong><span style="font-size:small;">M&atilde; phiếu thu: {Ma_Phieu}</span></strong></div><div class="mb25" style="text-align:center;"><em><span style="font-size:small;">Ng&agrave;y:&nbsp;</span></em><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;"><em>{Ngay_Thang_Nam}</em></span></div>
    <table width="100%">
        <tbody>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td width="24%"><span style="font-size:small;">Họ t&ecirc;n người nộp tiền:</span></td>
                        <td style="border-bottom:1px dotted \#000;">{Doi_Tac}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td style="white-space:nowrap;" width="24%"><span style="font-size:small;">Số điện thoại:</span></td>
                        <td style="border-bottom:1px dotted #000;">{So_Dien_Thoai}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td style="white-space:nowrap;" width="24%"><span style="font-size:small;">Địa chỉ:</span></td>
                        <td style="border-bottom:1px dotted #000;">{Dia_Chi}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td style="white-space:nowrap;" width="24%"><span style="font-size:small;">L&yacute; do nộp:</span></td>
                        <td style="border-bottom:1px dotted \#000;">{Ly_Do}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" height="20"></td>
        </tr>
        <tr>
            <td>
                <span style="font-size:small;">Số tiền: </span><strong><span style="font-size:small;">{Gia_Tri_Phieu}</span></strong>
            </td>
        </tr>
        <tr>
            <td>
                <p><span style="font-size:small;">Bằng chữ: </span><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;">{Gia_Tri_Bang_Chu}</span><em>&nbsp;</em></p>
            </td>
        </tr>
        <tr>
            <td colspan="2" height="10"></td>
        </tr>
        </tbody>
    </table>
    <div class="mb10" style="text-align:right;"><span style="font-size:small;">Ng&agrave;y .......... Th&aacute;ng .......... Năm ...............</span></div>
    <table width="100%">
        <tbody>
        <tr>
            <td align="center" width="33%"><strong><span style="font-size:small;">Người lập phiếu</span></strong></td>
            <td align="center" width="33%"><strong><span style="font-size:small;">Người nộp</span></strong></td>
            <td align="center" width="33%"><strong><span style="font-size:small;">Thủ quỹ</span></strong></td>
        </tr>
        </tbody>
    </table>
</div>