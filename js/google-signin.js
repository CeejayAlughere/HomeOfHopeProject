import { initializeApp } from 'https://www.gstatic.com/firebasejs/13.3.0/firebase-app.js';
import { getAuth, GoogleAuthProvider, signInWithPopup, setPersistence, inMemoryPersistence, signOut } from 'https://www.gstatic.com/firebasejs/13.3.0/firebase-auth.js';

const app = initializeApp({
  apiKey: 'AIzaSyDelQuzFdKgGpWb21-biU-oQ-jHwpybKgg',
  authDomain: 'home-of-hope-app.firebaseapp.com',
  projectId: 'home-of-hope-app',
  appId: '1:684071644834:web:4580b9943cc9bcade831fb'
});
const auth = getAuth(app);
const ready = setPersistence(auth, inMemoryPersistence);
const el = id => document.getElementById(id);
const provider = new GoogleAuthProvider();
provider.setCustomParameters({ prompt: 'select_account' });
let user = null;
let action = null;
function busy(value) {
  el('google-start').disabled = value;
  el('google-submit').disabled = value;
}
async function submit() {
  const response = await fetch('api/google_login.php', {
    method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ client: 'web', csrf: el('google-signin').dataset.csrf,
      idToken: await user.getIdToken(true),
      fullName: action === 'profile' ? el('google-name').value : '',
      idNumber: action === 'profile' ? el('google-id').value : '',
      password: action === 'link' ? el('google-password').value : '' })
  });
  const result = await response.json();
  el('google-password').value = '';
  if (response.ok && result.success) {
    await signOut(auth);
    window.location.assign('dashboard.php');
    return;
  }
  el('google-message').textContent = result.message || 'Sign-in failed. Please try again.';
  if (result.action === 'link' || result.action === 'profile') {
    action = result.action;
    el('google-complete').hidden = false;
    el('google-email').textContent = user.email;
    el('google-profile-fields').hidden = action !== 'profile';
    el('google-link-fields').hidden = action !== 'link';
    el('google-name').required = action === 'profile';
    el('google-id').required = action === 'profile';
    el('google-password').required = action === 'link';
    if (!el('google-name').value) el('google-name').value = result.displayName || user.displayName || '';
  }
}
function showError(error) {
  const messages = {
    'auth/popup-closed-by-user': 'Google sign-in was cancelled. You can try again.',
    'auth/popup-blocked': 'Allow popups for this website, then try again.',
    'auth/unauthorized-domain': 'This website domain must be authorised in Firebase.'
  };
  el('google-message').textContent = messages[error.code] || 'Could not finish sign-in. Check your connection and try again.';
}
el('google-start').addEventListener('click', async () => {
  busy(true);
  action = null;
  el('google-complete').hidden = true;
  el('google-password').value = '';
  el('google-name').value = '';
  el('google-id').value = '';
  el('google-message').textContent = 'Opening Google sign-in…';
  try { await ready; user = (await signInWithPopup(auth, provider)).user; await submit(); }
  catch (error) { showError(error); }
  finally { busy(false); }
});
el('google-complete').addEventListener('submit', async event => {
  event.preventDefault();
  busy(true);
  try { if (!user) throw new Error('Restart sign-in'); await submit(); }
  catch (error) { showError(error); }
  finally { el('google-password').value = ''; busy(false); }
});
