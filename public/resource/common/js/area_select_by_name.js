$(function() {


    // var Province,City,District;
    // Province = document.getElementById("Province");
    // City = document.getElementById("City");
    // District = document.getElementById("District");

    // 初始化
    // (function(){
    //     var ProvinceHtml = "";
    //     ProvinceHtml = "<option value=''>请先选择省</option>";
    //     region.forEach(function(index){
    //         ProvinceHtml += "<option value='"+index.provinceCode+"'>"+index.provinceName+"</option>";
    //     });
    //     Province.innerHTML = ProvinceHtml;
    //
    //
    // })()



    // Province.onchange = function(){
    //     var that = this;
    //     var CityHtml = "";
    //     CityHtml = "<option value=''>请先选择市</option>";
    //     // 初始化县
    //     region.forEach(function(index){
    //         if(index.provinceCode == that.value){
    //             index.mallCityList.forEach(function(child){
    //                 CityHtml += "<option value='"+child.cityCode+"'>"+child.cityName+"</option>";
    //             });
    //             City.innerHTML = CityHtml;
    //             return ;
    //         }
    //     });
    // };
    // City.onchange = function(){
    //     var that = this;
    //     var DistrictHtml = "";
    //     region.forEach(function(index){
    //         if(index.provinceCode == Province.value){
    //             index.mallCityList.forEach(function(child){
    //                 if(child.cityCode == that.value){
    //                     child.mallAreaList.forEach(function(sun){
    //                         DistrictHtml += "<option value='"+sun.areaCode+"'>"+sun.areaName+"</option>";
    //                     });
    //                     District.innerHTML = DistrictHtml;
    //                     return ;
    //                 }
    //             });
    //         }
    //     });
    // };




    var $province = $(".area_select_province");
    var $city = $(".area_select_city");
    var $district = $(".area_select_district");

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


        region.forEach(function(element){
            // ProvinceHtml += "<option value='"+element.provinceCode+"'>"+element.provinceName+"</option>";
            if(element.provinceName == $province_value)
            {
                ProvinceHtml += '<option value="' + element.provinceName + '" selected="selected">' + element.provinceName + '</option>';

                element.mallCityList.forEach(function(child, index){

                    if(child.cityName == $city_value)
                    {
                        CityHtml += '<option value="' + child.cityName + '" selected="selected">' + child.cityName + '</option>';

                        child.mallAreaList.forEach(function(grandchild, i){

                            if(grandchild.areaName == $district_value)
                            {
                                DistrictHtml += '<option value="' + grandchild.areaName + '" selected="selected">' + grandchild.areaName + '</option>';
                            }
                            else
                            {
                                DistrictHtml += "<option value='"+grandchild.areaName+"'>"+grandchild.areaName+"</option>";
                            }
                        })
                    }
                    else
                    {
                        // CityHtml += "<option value='"+child.cityCode+"'>"+child.cityName+"</option>";
                        CityHtml += "<option value='"+child.cityName+"'>"+child.cityName+"</option>";
                        // if(index == 0)
                        // {
                        //     child.mallAreaList.forEach(function(grandchild, i){
                        //         DistrictHtml += "<option value='"+grandchild.areaName+"'>"+grandchild.areaName+"</option>";
                        //     });
                        // }
                    }
                });

            }
            else
            {
                ProvinceHtml += "<option value='"+element.provinceName+"'>"+element.provinceName+"</option>";
            }
        });

        $province.html(ProvinceHtml);
        $city.html(CityHtml);
        $district.html(DistrictHtml);
    })();

    $province.on('change', function() {
        var $that = $(this);
        var $select_box = $that.parents('.area_select_box');
        var $city = $select_box.find('.area_select_city');
        var $district = $select_box.find('.area_select_district');

        var CityHtml = "";
        var DistrictHtml = "";
        CityHtml = "<option value=''>请选择市</option>";
        CityHtml = "";
        // 初始化城市
        region.forEach(function(element){
            if(element.provinceName == $that.val()){
                element.mallCityList.forEach(function(child, index){
                    // CityHtml += "<option value='"+child.cityCode+"'>"+child.cityName+"</option>";
                    CityHtml += "<option value='"+child.cityName+"'>"+child.cityName+"</option>";
                    if(index == 0)
                    {
                        child.mallAreaList.forEach(function(sun){
                            // DistrictHtml += "<option value='"+sun.areaCode+"'>"+sun.areaName+"</option>";
                            DistrictHtml += "<option value='"+sun.areaName+"'>"+sun.areaName+"</option>";
                        });
                    }
                });
            }
        });
        $city.html(CityHtml);
        $district.html(DistrictHtml);
        console.log($city.val());
        // $district.html("<option value=''>请先选择市</option>");
        return ;
    });

    $city.on('change', function() {
        var $that = $(this);
        var $select_box = $that.parents('.area_select_box');
        var $province = $select_box.find('.area_select_province');
        var $district = $select_box.find('.area_select_district');

        var DistrictHtml = "";
        region.forEach(function(element){
            if(element.provinceName == $province.val()){
                element.mallCityList.forEach(function(child, index){
                    if(child.cityName == $that.val()){
                        child.mallAreaList.forEach(function(sun){
                            // DistrictHtml += "<option value='"+sun.areaCode+"'>"+sun.areaName+"</option>";
                            DistrictHtml += "<option value='"+sun.areaName+"'>"+sun.areaName+"</option>";
                        });
                    }
                });
            }
        });
        $district.html(DistrictHtml);
        return ;
    });


});

