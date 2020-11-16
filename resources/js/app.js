require('./bootstrap');

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
    } else {
        document.getElementById("zip").setAttribute("disabled", "disabled");
        document.getElementById("zip").value = "";
    }
}

document.getElementById("zip").oninput = function() {
    if (document.getElementById("zip").value.length > 0) {
        document.getElementById('location').setAttribute("disabled", "disabled");
        document.getElementById("location").value = "";
        document.getElementById('radius').setAttribute("disabled", "disabled");

    } else {
        document.getElementById('location').removeAttribute("disabled");
        document.getElementById('radius').removeAttribute("disabled");
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


document.getElementById("searchSubmit").onclick = function() {
    var zip = document.getElementById("zip").value;
    var type = document.getElementById("type").options[document.getElementById("type").options.selectedIndex].value;

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
        callApi(url);
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
    callApi(url);
    return false;
}

function callApi(url) {
    document.getElementById("searchSubmit").classList.add("d-none");
    document.getElementsByClassName("lds-ripple")[0].classList.remove("d-none");
    fetch(url)
        .then((response) => {
            return response.json()
        })
        .then((data) => {
            console.log("data response", data)
            window.t = data
            document.getElementById("searchSubmit").classList.remove("d-none");
            document.getElementsByClassName("lds-ripple")[0].classList.add("d-none");
            BuildTable(data);
            document.getElementById("hits").innerHTML = data.results.length + " Treffer!";
            if (typeof data.referenz !== "undefined") {
                // document.getElementById("download-sheet-input").style.display = "block"; // inline
                document.getElementById("download-csv").classList.remove("d-none");
                document.getElementById("download-csv").href = "/download/" + data.referenz + "/" + type.value;
            } else if (data.results.length > 0) {
                // document.getElementById("download-sheet-input").style.display = "block"; // inline
                document.getElementById("download-csv").classList.remove("d-none");
                document.getElementById("download-csv").href = "/download/generate/" + zip.value + "/" + type.value;
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
        let table = document.getElementById("myTable").getElementsByTagName('tbody')[0];
        let row = table.insertRow();
        let cell1 = row.insertCell(0);
        let cell2 = row.insertCell(1);
        let cell3 = row.insertCell(2);
        cell1.innerHTML = value.name.substring(0, 40) + `<br/><small>Stand: ${value.updated_at}</small>`;
        cell2.innerHTML = `${value.street} ${value.street_number}, ${value.zip} ${value.place}`;
        cell3.innerHTML = `${value.phone} <br/>${value.website != null? value.website : ''}`;
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