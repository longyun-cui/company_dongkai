$(function() {


    $('.location-select-city-from-city').on('change', function() {
        var $that = $(this);
        var $select_box = $that.parents('.location-select-box');
        var $district = $select_box.find('.location-select-district-from-city');

        var CityHtml = "";
        var DistrictHtml = "";
        DistrictHtml = "<option value=''>请选行政区</option>";
        // 初始化地区
        $location_city.forEach(function(element)
        {
            if(element.value == $that.val()){
                element.children.forEach(function(child, index)
                {
                    DistrictHtml += "<option value='"+child.value+"'>"+child.label+"</option>";

                });
            }
        });
        $district.html(DistrictHtml);
        return ;
    });


});

