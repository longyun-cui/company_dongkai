{{--工单详情--}}
<div class="modal fade modal-wrapper" id="modal--for--project--item-detail">
    <div class="col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">项目详情</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered">
                <div class="box-body">


                    {{--订单ID--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">项目名称</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="project-name"></span>
                        </div>
                    </div>
                    {{--通话录音--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">项目要求</label>
                        <div class="col-md-8 ">
                            <span class="project-requirement"></span>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 _none">
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>