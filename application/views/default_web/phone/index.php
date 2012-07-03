<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />
<script type="text/javascript" src="/resources/addon/phone.js"></script>

<div id="boxes">

    <form id="frm_search" action="/phone/search" method="post">
        <div id="phone_fillter">
            <div id="items_fillter">
                <ul>
                    <li style="width:110px;">
                        <span class="fillter_input">
                            <div id="inout-group" data-toggle="buttons-radio" class="btn-group">
                                <button type="button" class="btn active" data-value="-1">All</button>
                                <button type="button" class="btn" data-value="1">In</button>
                                <button type="button" class="btn" data-value="0">Out</button>
                                <input type="hidden" id="inout" name="status" value="-1" />
                            </div>
                        </span>
                    </li>
                    <li style="width:215px; margin-left:10px;">
                        <span class="fillter_input">
                            <div id="calltype-group" data-toggle="buttons-radio" class="btn-group">
                                <button type="button" class="btn active" data-value="-1">All</button>
                                <button type="button" class="btn" data-value="0">Call</button>
                                <button type="button" class="btn" data-value="1">Voice Mails</button>
                                <button type="button" class="btn" data-value="2">Faxes</button>
                                <input type="hidden" id="calltype" name="status" value="-1" />
                            </div>
                        </span>
                    </li>
                    <li style="margin-left:10px;">
                        <span class="fillter_input">
                            <select id="date_range" name="date_range">
                                <option value="">- - - Select - - -</option>
                                <option value="1">Last Month</option>
                            </select>
                        </span>
                    </li>
                    <li>
                        <span class="fillter_input"><input type="text" id="date_range_from" name="date_range_from" class="inv-field datepicker" /></span>
                    </li>
                    <li>
                        <span class="fillter_input"><input type="text" id="date_range_to" name="date_range_to" class="inv-field datepicker" /></span>
                    </li>
                </ul>
            </div>
        </div>            
        
        <input type="hidden" id="page" name="page" />
	<input type="hidden" id="row_per_page" name="row_per_page" />
    </form>

    <div id="phone_cases">
        <div id="loader" style="display: none;"><img src="/resources/template/default_web/img/invoice/loading.gif" /></div>
        <div id="phone_list">
            <table id="tbl_phone" cellpadding="0" cellspacing="0" border="0" class="table table-striped">
            </table>
        </div>
    </div>
</div>