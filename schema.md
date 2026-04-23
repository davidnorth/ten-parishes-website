# Data structure

users
- name
- email
- password (hashed)
- salt

parishes
- name
- latitude, longitude
- image_id (Cloudinary public_id)
- slug (auto-generated from name)
- picture_id (Cloudinary public_id)
(has many venues, has many artists through venues)

venues
- parish_id
- name
- slug (auto-generated from name)
- latitude, longitude
(belongs to parish)
(has many artists)

artists
- venue_id
- type enum: exhibition, special, workshop
- name
- body_html
(belongs to venue)
(has many event dates)
(has many images)

images
- artist_id
- main (boolean)
- name
- image_id (cloundinary)
(belongs to artist)

event_date (a time range and location)
- artist_id
- date
- from_time
- to_time
(belongs to artist)
