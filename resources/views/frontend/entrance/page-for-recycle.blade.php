@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title')钢琴回收-{{ config('company.info.short_name') }}@endsection
@section('meta_author')@endsection
@section('meta_title')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection




{{--header--}}
@section('component-header')
    @include('frontend.component.header')
@endsection


{{--footer--}}
@section('component-footer')
    @include('frontend.component.footer')
@endsection




{{--custom-content--}}
@section('custom-content')

    {{--banner--}}
    @include('frontend.component.banner-for-rent-out-list')

    {{--main--}}
    @include('frontend.module.page-for-recycle')

@endsection




{{--script--}}
@section('custom-script')
<script>

    $(".show-modal-zc").on('click',function(){
        $('#dialog-zc').show();
        $('#dialog-jg').hide();
        $('#dialog-kp').hide();
        $('#grab-modal').modal();
    });

    $(".show-modal-jg").on('click',function(){
        $('#dialog-zc').hide();
        $('#dialog-jg').show();
        $('#dialog-kp').hide();
        $('#grab-modal').modal();
    });

    $(".show-modal-item").on('click',function(){
        $('#dialog-zc').hide();
        $('#dialog-jg').hide();
        $('#dialog-item').show();
        $('#grab-modal').modal();
    });

    $('#house-type-images').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: false,
        infinite: true,
        autoplay: true,
        speed: 1000,
        prevArrow: '<i class="fa fa-angle-left"></i>',
        nextArrow: '<i class="fa fa-angle-right"></i>',
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 767,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 600,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });


    $(function() {

        $('.lightcase-image').lightcase({
            maxWidth: 9999,
            maxHeight: 9999
        });

    });

</script>
@endsection
