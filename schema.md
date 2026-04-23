# Data structure

user
- name
- email
- password (hashed)
- salt

parishes
- name
- geopoint (allow open maps, gmaps etc to pinpoint)
- image_id (Cloudinary image id)
(has many venues, has many artists through venues)

venues
- parish_id
- name
- geopoint
(belongs to parish)
(has many artists)

artists
- location_id
- type enum: exhibition, special, workshop
- name
- body_html
- images (list of cloundinary image ids)
(belongs to venue)
(has many event dates)

events (a time range and location)
- artist_id
- date
- from_time
- to_time
(belongs to artist)



