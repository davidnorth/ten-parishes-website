

Next tasks

New fields for artists, each with field in admin form.


email
phone
short_description (textarea in admin)
picture_id (another cloundinary field for a profile picture, marked as optional in admin)


# Registration process
A public series of forms for artists to sign up to the event.
Broken down into multiple manageable steps.
When signing up they provide their venue details also so we're creating a venue along with the artist.
GET for form on each step post to same path then redirect to next step

/register - introduction page with 'Get started' CTA
/register/step-1 
  - artist details (name, email, phone, body_html (textarea)  profile picture (optional)
  - ensure unique email
/register/step-2
  - venue details (parish (select), location (map), address, what_3_words, directions, parking, refreshments, accessibility, dogs_allowed (boolean), venue_contact_name, venue_contact_phone)
  "Please enter the details of the venue where your event will take place."
/register/step-3
  - event dates, same as admin here
  "Please add the dates your event will be open to the public"
/regiser/step-4
  - images
    as in admin, we allow adding a list of images with Cloudinary upload. shall we reuse some code form admin here? 
GET /register/complete

# Considerations
How do we prevent abuse of the form? Please offer your suggestings when building a plan.





