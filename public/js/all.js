let rangeInput = document.getElementById("range");
document.getElementById("range-value").innerHTML = rangeInput.value;
rangeInput.onchange = function() {
    document.getElementById("range-value").innerHTML = this.value
};

function init() {
    document.getElementById("myList").innerHTML = "";
    start();
}

let _json, _json2 = {};
async function start() {
    // let _json, _json2 = null;
    console.log("step1-_json", _json)
    let input = document.getElementById("adress-input-field").value;
    let type = document.getElementById("type").options[document.getElementById("type").options.selectedIndex].value;
    let apiKey = 'AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA';
    let api_get_adress = "https://maps.googleapis.com/maps/api/geocode/json?address=%s,+CA&key=%s";
    let apiCallUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" + encodeURIComponent(input) + ",+CA&key=" + apiKey;
    let btnNewCrawl = document.getElementById("new-crawl-geodata-from-adress");
    console.log("input", input)


    console.log(fetch("api/getgeodatabyname?address=" + encodeURIComponent(input) + "&key=" + apiKey));
    return;
    if (_json == null) {
        // step 1	
        // check if place allreay in database
        try {
            let response = await fetch("api/searchbyplace?name=" + input + "&type=" + type);
            _json = await response.json();
        } catch (err) {
            // catches errors both in fetch and response.json
            alert("step1");
            console.log("err step 1", err)
        }
        console.log(_json)
        if (_json.length > 0) {
            for (let key in _json) {
                console.log(key, _json[key]);
            }

            Object.entries(_json).forEach(([key, value]) => {
                console.log({ value })
                myFunctionDB(value);
            });
        }
        btnNewCrawl.classList.remove("hide-this-element")
        return;


        try {
            let response = await fetch(apiCallUrl);
            _json = await response.json();
        } catch (err) {
            // catches errors both in fetch and response.json
            alert("step1");
            console.log("err step 1", err)
        }
        console.log("step_1", _json);
    } else {
        alert("old")
    }
    // step 2
    // "CqQCHwEAAMDjIz4BOUdG20RV8dn9OGtZjdVvL9lNWLhG_PcL_vFLsCDrKtOdTuvSbRHgzAH6zYk04NsdOrBPiKqibZ-gxwd5qmcD05AYfGgxekW1yqXcw0tBHeDpC32ASgkM4a6gY3voKvDF-zNPtaHUCKihwQC0VGU5UDo97Pf0rCumGgrUfqRPmnbQDO_6NjzL0InA-9Hsq3oaAr4u7C7tchg8YymbbNelXlTgKfc5H45XYtVDiGbAjKY3-0LapZHrChiGEF0tjVhFUUQTHe0M-ECjxgfcfOO7iuhXlkRfTazCbcnUC4O5BiS-YwvrabE4nw4Z45JXYsoG_6tzM2eBTXya2bMwD0TdX1ZZN3cgk13VWeW2HozVJTQcfhfOf0mQWHdLGRIQWsza_qoarLKU3FuwZhE4BRoUhvwV4DnkjgHKsgwcA_s-lwK2gCA"			
    await placesNearbyCall();



    /*
    let oReq = new XMLHttpRequest();
    oReq.addEventListener("load", reqListener);
    oReq.open("GET", "apiCallUrl");
    oReq.send();			
    */
    // console.log();
    // alert( sprintt (api_get_adress,[input,apiKey]) );  							
}



async function placesNearbyCall() {
    let pagi = false;
    //let url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location="+data.results[0].geometry.location.lat+","+data.results[0].geometry.location.lng+"&radius=1500&type=restaurant&keyword=cruise&key=AIzaSyDlG7DSQ99FNnOb8Z2tH9JpnYfVxsx4jFA";			
    // let _json2 = url = null;
    console.log("_json2", _json2);
    console.log("pagetokeb", _json2["next_page_token"])
    let url, pagetoken = '';
    if (typeof _json2["next_page_token"] === 'undefined') {
        console.log("no pagetoken");

    } else {
        console.log("yes with pagetoken founded")
        pagetoken = _json2["next_page_token"];
    }


    console.log("pagetoken stted" + pagetoken)
    if (pagetoken !== '') {
        alert("with next page token")
        url = "api/start" + "?lat=" + _json.results[0].geometry.location.lat + "&lng=" + _json.results[0].geometry.location.lng + "&pagetoken=" + pagetoken;
    } else {
        alert("no page token")
        url = "api/start" + "?lat=" + _json.results[0].geometry.location.lat + "&lng=" + _json.results[0].geometry.location.lng
    }

    try {
        let response = await fetch(url);
        console.log("url step 2", url);
        _json2 = await response.json();
        //document.getElementById("output").innerHTML = _json2.results;
        Object.entries(_json2.results).forEach(([key, value]) => {
            console.log(`${key}: ${value.name}`)
            myFunction(value);
        });
    } catch (err) {
        // catches errors both in fetch and response.json
        alert("step2");
        console.log("err step 2", err);
    }

    // show next btn or not
    if (typeof _json2["next_page_token"] === 'undefined') {
        pagi = false;
        if (document.getElementById("next") !== null) {
            console.log("pagi false true set")
            document.getElementById("next").remove();
        }
    } else {
        pagi = true;
        // set Next Btn (pagination)
        if (document.getElementById("next") == null) {
            console.log("pagi true set")
            btn = document.createElement("BUTTON"); // Create a <button> element
            btn.innerHTML = "CLICK ME";
            btn.setAttribute("id", "next");
            btn.onclick = function() { start() };
            document.body.appendChild(btn);
        }
    }


    console.log("_json2", _json2);
}

function myFunctionDB(obj) {
    var node = document.createElement("LI");
    var textnode = document.createTextNode(obj.name + "-" + obj.place_id);
    node.appendChild(textnode);
    document.getElementById("myList").appendChild(node);
}

function myFunction(obj) {
    var node = document.createElement("LI");
    var textnode = document.createTextNode(obj.name + "-" + obj.place_id + "-" + obj.vicinity + "-" + obj.types.join());
    node.appendChild(textnode);
    document.getElementById("myList").appendChild(node);
}

async function apiCall(url) {
    let json = null;
    try {
        let response = await fetch(url);
        return await response.json();

    } catch (err) {
        // catches errors both in fetch and response.json
        alert(err);
    }
}

/*
var geoData = {};		  
function appendData (data) {
   console.log (data);
   document.querySelector ("div").innerHTML = JSON.parse(data).results[0].geometry.location.lat;
   // let _data = JSON.parse(data);
    // geoData = _data.results[0].geometry.location
    
}
*/

function sprintt(s, params) {

    // We made a clone of the "s" string  
    var newS = s,

        // and initializing a loop index  
        currentIndex = 0;

    // Param will be a Json made list of placeholders.  
    for (param in params) {

        // Index will start by 1  
        currentIndex += 1;

        if (!isNaN(param)) {
            // If the params object is an indexed array  

            var value = params[param];

            // Actually the method will support only numbers (d)   
            // and strings (s).  
            // Float numbers will be threath as numbers.  
            // Any support for octal/hex transformation.  
            // Neither for string filler.   
            if (isFinite(value)) {
                param = 'd';
            } else {
                param = 's';
            }
            // the regular expression must check only for the first   
            // occourrence of the given placeholder (as in sprintf)  
            var regEx = new RegExp('%' + param);
            newS = newS.replace(regEx, value);

            // and then must replace all the %x$1 occourrences in the   
            // string, where "x" is the current index entry (as in sprintf)  
            regEx = new RegExp('%' + currentIndex + '$' + param, 'g');
        } else {
            // Else we have a json "key/value" pair object and we have   
            // to replace all the given tokens  
            var regEx = new RegExp('%' + param, 'g');
            value = params[param];
        }
        newS = newS.replace(regEx, value);

    }
    return newS;
}