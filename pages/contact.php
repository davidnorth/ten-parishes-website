<?php
if ($req->isPost()) {
    return redirect('/contact?sent=1');
}
?>
<h1>Contact Us</h1>
<?php if (isset($req->params['sent'])): ?>
<p>Thanks for your message — we'll be in touch.</p>
<?php endif ?>
<form method="post" action="/contact">
  <label>Name <input type="text" name="name"></label>
  <label>Email <input type="email" name="email"></label>
  <label>Message <textarea name="message"></textarea></label>
  <button type="submit">Send</button>
</form>
