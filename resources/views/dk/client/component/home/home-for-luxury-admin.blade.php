<div class="row tab-pane active" id="tab-home">


    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="box box-warning box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">设置</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="toggle-box" style="padding:10px 15px;height:40px;clear:both;">
                        <span class="pull-left">
                            <b class="toggle-handle-text-">自动派单</b>
                            @if($me->client_er->is_automatic_dispatching)
                                <span class="toggle-handle-text">【开启中】</span>
                            @else
                                <span class="toggle-handle-text">【已关闭】</span>
                            @endif
                        </span>
                        <button id="toggle-button-for-automatic-dispatching" class="toggle-button pull-right
                            @if($me->client_er->is_automatic_dispatching) toggle-button-on
                            @else toggle-button-off
                            @endif
                        ">
                            <span class="toggle-handle"></span>
                        </button>
                    </li>
                    <li class="_none" style="padding:10px 15px;height:40px;clear:both;">
                        <span class="pull-left">
                            <b class="toggle-handle-text-">一键派单</b>
                        </span>
                        <button id="admin-summit-for-automatic-dispatching" class="toggle-button pull-right">
                            派单
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>


</div>