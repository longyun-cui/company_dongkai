$(function() {


    var $province = $(".location-select-province");
    var $city = $(".location-select-city");
    var $district = $(".location-select-district");


    // 初始化
    (function(){

        var ProvinceHtml = "";
        var CityHtml = "";
        var DistrictHtml = "";

        ProvinceHtml = "<option value=''>请选择省</option>";
        CityHtml = "<option value=''>请先选择省</option>";
        DistrictHtml = "<option value=''>请先选择市</option>";

        var $province_value = $province.val();
        var $city_value = $city.val();
        var $district_value = $district.val();


        $location.forEach(function(element)
        {
            // ProvinceHtml += "<option value='"+element.value+"'>"+element.label+"</option>";
            if(element.label == $province_value)
            {
                ProvinceHtml += '<option value="' + element.value + '" selected="selected">' + element.label + '</option>';

                element.children.forEach(function(child, index)
                {

                    if(child.value == $city_value)
                    {
                        CityHtml += '<option value="' + child.value + '" selected="selected">' + child.label + '</option>';

                        child.children.forEach(function(grandchild, i){

                            if(grandchild.label == $district_value)
                            {
                                DistrictHtml += '<option value="' + grandchild.value + '" selected="selected">' + grandchild.label + '</option>';
                            }
                            else
                            {
                                DistrictHtml += "<option value='"+grandchild.value+"'>"+grandchild.label+"</option>";
                            }
                        })
                    }
                    else
                    {
                        CityHtml += "<option value='"+child.value+"'>"+child.label+"</option>";
                        // if(index == 0)
                        // {
                        //     child.children.forEach(function(grandchild, i)
                        //     {
                        //         DistrictHtml += "<option value='"+grandchild.label+"'>"+grandchild.label+"</option>";
                        //     });
                        // }
                    }
                });

            }
            else
            {
                ProvinceHtml += "<option value='"+element.value+"'>"+element.label+"</option>";
            }
        });

        $province.html(ProvinceHtml);
        $city.html(CityHtml);
        $district.html(DistrictHtml);

    })();


    // 省份变更
    $('.location-select-province').on('change', function() {
        var $that = $(this);
        var $select_box = $that.parents('.location-select-box');
        var $city = $select_box.find('.location-select-city');
        var $district = $select_box.find('.location-select-district');

        var CityHtml = "";
        var DistrictHtml = "";
        CityHtml = "<option value=''>请选择市</option>";
        CityHtml = "";
        // 初始化城市
        $location.forEach(function(element)
        {
            if(element.value == $that.val())
            {
                element.children.forEach(function(child, index)
                {
                    CityHtml += "<option value='"+child.value+"'>"+child.label+"</option>";
                    if(index == 0)
                    {
                        child.children.forEach(function(sun){
                            DistrictHtml += "<option value='"+sun.value+"'>"+sun.label+"</option>";
                        });
                    }
                });
            }
        });
        $city.html(CityHtml);
        $district.html(DistrictHtml);
        // $district.html("<option value=''>请先选择市</option>");
        return ;
    });


    // 城市变更
    $('.location-select-city').on('change', function() {
        var $that = $(this);
        var $select_box = $that.parents('.location-select-box');
        var $province = $select_box.find('.location_select_province');
        var $district = $select_box.find('.location-select-district');

        var DistrictHtml = "";
        $location.forEach(function(element)
        {
            if(element.value == $province.val())
            {
                element.children.forEach(function(child, index)
                {
                    if(child.value == $that.val())
                    {
                        child.children.forEach(function(son)
                        {
                            DistrictHtml += "<option value='"+son.value+"'>"+son.label+"</option>";
                        });
                    }
                });
            }
        });
        $district.html(DistrictHtml);
        return ;
    });


});

