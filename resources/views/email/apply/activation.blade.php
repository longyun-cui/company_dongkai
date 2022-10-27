您已经报名 {{ $title }} ，<br>
请点击链接完成报名: <a href="http://softorg.cn/apply/activation?email={{ $email }}&activity={{ $activity_id }}&apply={{ $apply_id }}">点击链接确认报名</a> <br>
如果未跳转，请复制一下链接地址在浏览器中打开。<br>
http://softorg.cn/apply/activation?email={{ $email }}&activity={{ $activity_id }}&apply={{ $apply_id }} <br><br>

@if($is_sign == 1) 您的签到密码为：{{ $password }}。 @endif