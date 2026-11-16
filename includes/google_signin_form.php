<?php
if (empty($_SESSION['google_csrf'])) $_SESSION['google_csrf'] = bin2hex(random_bytes(32));
?>
<div id="google-signin" class="mt-4" data-csrf="<?php echo htmlspecialchars($_SESSION['google_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
    <p class="text-center">Or use your Google account</p>
    <button id="google-start" type="button" class="btn btn-outline-dark btn-block">Continue with Google</button>
    <p id="google-message" role="status" class="mt-3" aria-live="polite"></p>
    <form id="google-complete" hidden>
        <p id="google-email" class="font-weight-bold"></p>
        <div id="google-profile-fields" hidden>
            <div class="form-group"><label for="google-name">Full name</label><input id="google-name" class="form-control" maxlength="100" autocomplete="name"></div>
            <div class="form-group"><label for="google-id">South African ID number</label><input id="google-id" class="form-control" inputmode="numeric" maxlength="13" pattern="[0-9]{13}"></div>
        </div>
        <div id="google-link-fields" class="form-group" hidden>
            <label for="google-password">Existing website password</label>
            <input id="google-password" type="password" class="form-control" autocomplete="current-password">
        </div>
        <button id="google-submit" class="btn btn-custom btn-block" type="submit">Continue</button>
    </form>
</div>
<script type="module" src="js/google-signin.js"></script>
