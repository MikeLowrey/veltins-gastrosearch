// require('./bootstrap');

var searchInput = 'location';
autocomplete = new google.maps.places.Autocomplete((document.getElementById(searchInput)), {
    types: ['geocode'],
});
// Set initial restrict to the greater list of countries.
autocomplete.setComponentRestrictions({
    country: ["de"],
});
google.maps.event.addListener(autocomplete, 'place_changed', function() {
    var near_place = autocomplete.getPlace();
    window.t = near_place;
    console.log("near_place", near_place);
    document.getElementById('latitude').value = near_place.geometry.location.lat();
    document.getElementById('longitude').value = near_place.geometry.location.lng();
    document.getElementById('place-id').value = near_place.place_id;
    document.getElementById('formatted-address').value = near_place.formatted_address;
});

document.getElementById('location').onchange = function() {
    document.getElementById('latitude').value = '';
    document.getElementById('longitude').value = '';
    document.getElementById('place-id').value = '';
};

document.getElementById('location').oninput = function() {
    if (document.getElementById('location').value.length == 0) {
        document.getElementById("zip").removeAttribute("disabled");
        document.getElementById("type1").removeAttribute("disabled");
    } else {
        document.getElementById("zip").setAttribute("disabled", "disabled");
        document.getElementById("zip").value = "";
        document.getElementById("type1").setAttribute("disabled", "disabled");
        document.getElementById("type1").selectedIndex = "0";
    }
}

document.getElementById("zip").oninput = function() {
    if (document.getElementById("zip").value.length > 0) {
        document.getElementById('location').setAttribute("disabled", "disabled");
        document.getElementById("location").value = "";
        document.getElementById('radius').setAttribute("disabled", "disabled");
        document.getElementById('type2').setAttribute("disabled", "disabled");
        document.getElementById("type2").selectedIndex = "0";

    } else {
        document.getElementById('location').removeAttribute("disabled");
        document.getElementById('radius').removeAttribute("disabled");
        document.getElementById('type2').removeAttribute("disabled", "disabled");
    }
}

document.getElementById('type1').oninput = function() {
    if (document.getElementById("type1").options.selectedIndex !== 0) {
        document.getElementById("location").setAttribute("disabled", "disabled");
        document.getElementById("radius").setAttribute("disabled", "disabled");
        document.getElementById("type2").setAttribute("disabled", "disabled");
    } else {
        document.getElementById("location").removeAttribute("disabled");
        document.getElementById("radius").removeAttribute("disabled");
        document.getElementById("type2").removeAttribute("disabled");
    }
}

document.getElementById('type2').oninput = function() {
    if (document.getElementById("type2").options.selectedIndex !== 0) {
        document.getElementById("zip").setAttribute("disabled", "disabled");
        document.getElementById("type1").setAttribute("disabled", "disabled");
    } else {
        document.getElementById("zip").removeAttribute("disabled");
        document.getElementById("type1").removeAttribute("disabled");
    }
}

/*var ext = '';
document.getElementById("select-ext").onchange = function() {
    ext = document.getElementById("select-ext").options[document.getElementById("select-ext").options.selectedIndex].value;
    console.log("ext", ext)
    console.log("ext", document.getElementById("download-csv").href)
    if (document.getElementById("download-csv").href.length == 0) {
        alert();
    }
    document.getElementById("select-ext").setAttribute("disabled", "disabled");
    document.getElementById("download-csv").href = document.getElementById("download-csv").href + "." + ext
    document.getElementById("download-csv").removeAttribute("disabled");
}
*/
/*
document.getElementById("download-csv").onclick = function() {
    let link = document.getElementById("download-csv").href;
    console.log("link", link);
    location.href = link;
    return false;
}
*/


document.getElementById("searchSubmit-1").onclick = function() {
    console.log("clicked");
    var zip = document.getElementById("zip").value;
    if (zip.length >= 2) {
        var type = document.getElementById("type1").options[document.getElementById("type1").options.selectedIndex].value;
    } else {
        var type = document.getElementById("type2").options[document.getElementById("type2").options.selectedIndex].value;
    }


    if (type == "0") {
        document.querySelector('#custom-error-alert').classList.add("in")
        setTimeout(function() {
            document.querySelector('#custom-error-alert').classList.remove("in")
        }, 3000);
        return;
    }

    document.getElementById("myTable").getElementsByTagName('tbody')[0].innerHTML = ""

    if (zip != '') {
        let url = 'api/searchbyzip/' + zip + '/' + type;
        callApi(url, "searchSubmit-1");
        return;
    }

    // clear outpulist and list element
    document.getElementById("myList").innerHTML = ''

    let lat = document.getElementById('latitude').value;
    let lng = document.getElementById('longitude').value;
    let placeid = document.getElementById('place-id').value;

    if (type == "0" || document.getElementById('location').value == "") {
        document.querySelector('#custom-error-alert').classList.add("in")
        setTimeout(function() {
            document.querySelector('#custom-error-alert').classList.remove("in")
        }, 3000);
        return;
    }
    let radius = document.getElementById('radius').value;
    let formatted_address = encodeURI(document.getElementById('formatted-address').value)
    console.log("formatted_address", formatted_address)
        // type , radius
    let url = "api/call" + "?lat=" + lat + "&lng=" + lng + "&type=" + type + "&radius=" + radius + "&placeid=" + placeid + "&formattedaddress=" + formatted_address;
    callApi(url, "searchSubmit-1");
    return false;
}

document.getElementById("searchSubmit-2").onclick = function() {
    console.log("clicked");
    var zip = document.getElementById("zip").value;
    if (zip.length >= 2) {
        var type = document.getElementById("type1").options[document.getElementById("type1").options.selectedIndex].value;
    } else {
        var type = document.getElementById("type2").options[document.getElementById("type2").options.selectedIndex].value;
    }


    if (type == "0") {
        document.querySelector('#custom-error-alert').classList.add("in")
        setTimeout(function() {
            document.querySelector('#custom-error-alert').classList.remove("in")
        }, 3000);
        return;
    }

    document.getElementById("myTable").getElementsByTagName('tbody')[0].innerHTML = ""

    if (zip != '') {
        let url = 'api/searchbyzip/' + zip + '/' + type;
        callApi(url, "searchSubmit-2");
        return;
    }

    // clear outpulist and list element
    document.getElementById("myList").innerHTML = ''

    let lat = document.getElementById('latitude').value;
    let lng = document.getElementById('longitude').value;
    let placeid = document.getElementById('place-id').value;

    if (type == "0" || document.getElementById('location').value == "") {
        document.querySelector('#custom-error-alert').classList.add("in")
        setTimeout(function() {
            document.querySelector('#custom-error-alert').classList.remove("in")
        }, 3000);
        return;
    }
    let radius = document.getElementById('radius').value;
    let formatted_address = encodeURI(document.getElementById('formatted-address').value)
    console.log("formatted_address", formatted_address)
        // type , radius
    let url = "api/call" + "?lat=" + lat + "&lng=" + lng + "&type=" + type + "&radius=" + radius + "&placeid=" + placeid + "&formattedaddress=" + formatted_address;
    callApi(url, "searchSubmit-2");
    return false;
}

function callApi(url, btnElem) {
    console.log("btnElem", btnElem);
    document.getElementById(btnElem).classList.add("d-none");
    document.getElementsByClassName("lds-ripple")[0].classList.remove("d-none");
    fetch(url)
        .then((response) => {
            return response.json()
        })
        .then((data) => {
            console.log("data response", data)
            window.t = data
            document.getElementById(btnElem).classList.remove("d-none");
            document.getElementsByClassName("lds-ripple")[0].classList.add("d-none");
            BuildTable(data);
            document.getElementById("hits").innerHTML = data.results.length + " Treffer!";
            if (typeof data.referenz !== "undefined" && data.referenz !== '') {
                // document.getElementById("download-sheet-input").style.display = "block"; // inline
                document.getElementById("download-csv").classList.remove("d-none");
                document.getElementById("download-csv").href = "/download/" + data.referenz + "/" + type2.value;
            } else if (data.results.length > 0 && data.referenz == '') {
                // document.getElementById("download-sheet-input").style.display = "block"; // inline
                document.getElementById("download-csv").classList.remove("d-none");
                document.getElementById("download-csv").href = "/download/generate/" + zip.value + "/" + type1.value;
                console.log("zip", zip);
            } else {
                document.getElementById("download-csv").classList.add("d-none"); //.style.display = "none";
            }
        });
}



function BuildTable(obj) {
    if (obj.length == 0) {
        return;
    }
    Object.entries(obj.results).forEach(([key, value]) => {
        let time = new Date(value.updated_at);
        let timeString = time.getDate() + '.' + (time.getUTCMonth() + 1) + '.' + time.getUTCFullYear();
        let table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
        let row = table.insertRow();
        let cell1 = row.insertCell(0);
        let cell2 = row.insertCell(1);
        let cell3 = row.insertCell(2);
        let cell4 = row.insertCell(3);
        let cell5 = row.insertCell(4);
        let cell6 = row.insertCell(5);
        let cell7 = row.insertCell(6);
        let cell8 = row.insertCell(7);
        let cell9 = row.insertCell(8);
        //let cell6 = row.insertCell(5);
        cell1.innerHTML = value.name.substring(0, 40);
        cell2.innerHTML = `${value.street} ${value.street_number}`;
        cell3.innerHTML = `${value.zip}`;
        cell4.innerHTML = `${value.place}`;
        cell5.innerHTML = `${value.phone !== null? value.phone : '-'}`;

        cell6.innerHTML = `${value.website !== null? value.website : '-'}`;
        cell7.innerHTML = `${value.rating !== null? value.rating+'<small>/5</small>' : '0'}`;
        cell8.innerHTML = `${value.user_ratings_total !== null? value.user_ratings_total : '0'}`;

        cell9.innerHTML = `<span class="popup" onclick="showPopUp(${value.id})"><svg class="info-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" class="svg-inline--fa fa-info-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path></svg>
        <span class="popuptext" id="popup-${value.id}">Gecrawlt</br>${timeString}</span>
      </span>`;
        //cell6.innerHTML = `<span class="popup" onclick="showPopUp(${value.id})"><svg class="info-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" class="svg-inline--fa fa-info-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path></svg>
        //    <span class="popuptext" id="popup-${value.id}">Gecrawlt</br>${timeString}</span>
        //  </span>`      
    });
}

function BuildList(obj) {
    Object.entries(obj.results).forEach(([key, value]) => {
        console.log(`${key}: ${value.name}`)
        let node = document.createElement("LI");
        let textnode = document.createTextNode(value.name + "-" + value.place_id + "-" + value.formatted_address + "-" + value.types);
        // let textnode = document.createTextNode(value.join());
        node.appendChild(textnode);
        document.getElementById("myList").appendChild(node);
    });

}