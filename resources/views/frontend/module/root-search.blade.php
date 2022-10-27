{{--Search--}}
<div id="advance-search" class="main-page clearfix _none">
    <div class="container main-container">
        <button class="btn top-btn">Find Your Place</button>
        <form action="index.html#" id="adv-search-form" class="clearfix">
            <fieldset>
                <select name="location" id="main-location">
                    <option value="">All Cities</option>
                    <option value="chicago"> Chicago</option>
                    <option value="los-angeles"> Los Angeles</option>
                    <option value="miami"> Miami</option>
                    <option value="new-york"> New York</option>
                </select>
                <select name="sub-location" id="property-sub-location">
                    <option value="">All Areas</option>
                    <option value="brickell" > Brickell</option>
                    <option value="brickyard" > Brickyard</option>
                    <option value="bronx" > Bronx</option>
                    <option value="brooklyn" > Brooklyn</option>
                    <option value="coconut-grove" > Coconut Grove</option>
                    <option value="downtown" > Downtown</option>
                    <option value="eagle-rock" > Eagle Rock</option>
                    <option value="englewood" > Englewood</option>
                    <option value="hermosa" > Hermosa</option>
                    <option value="hollywood" > Hollywood </option>
                    <option value="lincoln-park" > Lincoln Park</option>
                    <option value="manhattan" > Manhattan</option>
                    <option value="midtown" > Midtown</option>
                    <option value="queens" > Queens</option>
                    <option value="westwood" > Westwood </option>
                    <option value="wynwood" > Wynwood</option>
                </select>
                <select id="property-status" name="status">
                    <option value="">All Status</option>
                    <option value="for-rent"> For Rent</option>
                    <option value="for-sale"> For Sale</option>
                    <option value="foreclosures"> Foreclosures</option>
                    <option value="new-costruction"> New Costruction</option>
                    <option value="new-listing"> New Listing</option>
                    <option value="open-house"> Open House</option>
                    <option value="reduced-price"> Reduced Price</option>
                    <option value="resale"> Resale</option>
                </select>
                <select id="property-type" name="type" >
                    <option value="">All Types</option>
                    <option value="apartment"> Apartment</option>
                    <option value="condo"> Condo</option>
                    <option value="farm"> Farm</option>
                    <option value="loft"> Loft</option>
                    <option value="lot"> Lot</option>
                    <option value="multi-family-home"> Multi Family Home</option>
                    <option value="single-family-home"> Single Family Home</option>
                    <option value="townhouse"> Townhouse</option>
                    <option value="villa"> Villa</option>
                </select>
                <select name="bedrooms" id="property-beds">
                    <option value="">Beds</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="any">Any</option>
                </select>
                <select name="bathrooms" id="property-baths">
                    <option value="">Bathrooms</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="any">Any</option>
                </select>
                <input type="text" name="min-area" id="property-min-area" placeholder="Min Area (sqft)">
                <input type="text" name="max-area" id="property-max-area" placeholder="Max Area (sqft)">
                <select name="min-price" id="property-min-price">
                    <option value="any" selected="selected">Min Price</option>
                    <option value="1000">$1000</option>
                    <option value="5000">$5000</option>
                    <option value="10000">$10000</option>
                    <option value="50000">$50000</option>
                    <option value="100000">$100000</option>
                    <option value="200000">$200000</option>
                    <option value="300000">$300000</option>
                    <option value="400000">$400000</option>
                    <option value="500000">$500000</option>
                    <option value="600000">$600000</option>
                    <option value="700000">$700000</option>
                    <option value="800000">$800000</option>
                    <option value="900000">$900000</option>
                    <option value="1000000">$1000000</option>
                    <option value="1500000">$1500000</option>
                    <option value="2000000">$2000000</option>
                    <option value="2500000">$2500000</option>
                    <option value="5000000">$5000000</option>
                </select>
                <select name="max-price" id="property-max-price" >
                    <option value="any" selected="selected">Max Price</option>
                    <option value="5000">$5000</option>
                    <option value="10000">$10000</option>
                    <option value="50000">$50000</option>
                    <option value="100000">$100000</option>
                    <option value="200000">$200000</option>
                    <option value="300000">$300000</option>
                    <option value="400000">$400000</option>
                    <option value="500000">$500000</option>
                    <option value="600000">$600000</option>
                    <option value="700000">$700000</option>
                    <option value="800000">$800000</option>
                    <option value="900000">$900000</option>
                    <option value="1000000">$1000000</option>
                    <option value="1500000">$1500000</option>
                    <option value="2000000">$2000000</option>
                    <option value="2500000">$2500000</option>
                    <option value="5000000">$5000000</option>
                    <option value="10000000">$10000000</option>
                </select>
            </fieldset>
            <button type="submit" class="btn btn-default btn-lg text-center">Search <br class="hidden-sm hidden-xs"> Property</button>
        </form>
    </div>
</div>