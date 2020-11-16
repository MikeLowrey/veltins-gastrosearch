# Veltins Gastrosearch
## Install
composer install
npm install
for changes on style or js use npm run dev or use the npm watch hot command for observing


## Database
![DB Schema](./veltins-db-relations.png)

## Cache
You can set a cache runtime in days for this application. On the first call new data is taken from the Google Places Api. On the second call it is checked.

Requests with parameters are first checked if they are already in the database. If yes, the system checks if the data is older than the allowed cache duration. If not, the data is loaded from the database. If yes, a new Google Places Api request is started. The old records are overwritten with the new ones.

It is possible to query for individual categories. For example cinema, bars, restaurants etc. It is also allowed to query all categories at once (all). 
Future requests with the same Place_ID and radius but an explicit category are first checked in the database. If a similar request has already been made with the category "all" only, these records will be filtered by the explicit category. 

**Tip: In the response JSON you will find the field dev_comment. Here you find some notes about the last request and caching. The field cached_data give you an short answear about you last request.**

## Api
**Get Data by place id, lat, lng, type, radius**
```bash
curl -X GET \
-G "http://veltins.sejka-friends.de/api/call?lat=51.3099808&lng=8.5253748&type=night_club&radius=1500&placeid=ChIJF4K8n8Xqu0cRMJwlq_9gJxw&formattedaddress=59939%20Olsberg,%20Germany"
-H "Content-Type: application/json" \
-H "Accept: */*"
```

Response Statuscode 200
```json
{
    "status":"OK",
    "results":[
        {
            "id":1,
            "place_id":"ChIJJzgXt-mUu0cRQH1p5ZmSkHE",
            "location":"{\"lat\":51.31xx,\"lng\":8.52xx}",
            "name":"DJ Tall",
            "types":"night_club,bar",
            "place":"Olsberg",
            "zip":"59939",
            "street":"Am Kleinen Berg",
            "street_number":"19a",
            "country":"Germany",
            "phone":null,
            "website":null,
            "formatted_address":"Am Kleinen Berg 19a, 59939 Olsberg, Germany",
            "user_ratings_total":1,
            "created_at":"2020-10-30 09:52:15",
            "updated_at":"2020-11-16 09:58:13"
        }
    ],
    "referenz":2,
    "dev_comment":"Founded User Request and loaded from Cache.",
    "cached_data":"yes"}
```
Response Statuscode 200

**Get data by zipcode and type**
```bash
curl -X GET \
-G "http://veltins.sejka-friends.de/api/searchbyzip/59939/"
-H "Content-Type: application/json" \
-H "Accept: */*"
```
```json
{
    "status":"OK",
    "results":[
        {
            "id":1,
            "place_id":"ChIJJzgXt-mUu0cRQH1p5ZmSkHE",
            "location":"{\"lat\":51.31xx,\"lng\":8.52xx}",
            "name":"DJ Tall",
            "types":"night_club,bar",
            "place":"Olsberg",
            "zip":"59939",
            "street":"Am Kleinen Berg",
            "street_number":"19a",
            "country":"Germany",
            "phone":null,
            "website":null,
            "formatted_address":"Am Kleinen Berg 19a, 59939 Olsberg, Germany",
            "user_ratings_total":1,
            "created_at":"2020-10-30 09:52:15",
            "updated_at":"2020-11-16 09:58:13"
        }
    ],
    "referenz":2,
    "dev_comment":"Filter cached Data only by zipcode. Note: Zipcode search allways deliver allways from cache.",
    "cached_data":"yes"}
```
Response Statuscode 200
**Note: Zipcode Search allways response allways cached data. If you haven't an result you try to search by location first.**


**Set Fileformat for downloads**
```bash
curl -X PUT \
-G "http://veltins.sejka-friends.de/api/settings/file_format/xlsx"
-H "Content-Type: text/html; charset=UTF-8" \
-H "Accept: */*"
```

Response Statuscode 200




**Set Cache duration in days**
```bash
curl -X PUT \
-G "http://veltins.sejka-friends.de/api/settings/cache_duration/[1-9]{1,3}"
-H "Content-Type: text/html; charset=UTF-8" \
-H "Accept: */*"
```
Response Statuscode 200


**Get all Setting values (key , value)**
```bash
curl -X GET \
-G "http://veltins.sejka-friends.de/api/settings/"
-H "Content-Type: application/json" \
-H "Accept: */*"
```
```json
{
    "file_format":"xlsx",
    "cache_duration":"7",
    "sleeptime_google_api":"1",
    ...
}
```
Response Statuscode 200

