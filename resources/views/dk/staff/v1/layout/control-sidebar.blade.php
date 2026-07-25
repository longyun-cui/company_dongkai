<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark" style="padding:54px 4px 16px;">


{{--    <div class="col-md-12">--}}
        <div class="box box-widget widget-user-2">
            <div class="box-footer no-padding">
                <ul class="nav nav-stacked">
                    @if(!empty($project_list__for__dental) && count($project_list__for__dental) > 0)
                        @foreach($project_list__for__dental as $v)
                        <li>
                            <a class="modal-show--for--project--item-detail"
                               data-modal-id="modal--for--project--item-detail"
                               data-name="{{ $v->name }}"
                               data-requirement="{{ $v->requirement }}"
                            >
                                {{ $v->name }} <span class="pull-right badge bg-blue">详情</span>
                            </a>
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
{{--    </div>--}}

</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>