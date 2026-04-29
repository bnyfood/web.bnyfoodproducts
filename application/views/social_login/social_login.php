<div class="social-login-page">
  <div class="social-login-card">
    <div class="logo-wrap">
      <?php if (!empty($logo_url)) { ?>
        <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
      <?php } else { ?>
        <span class="logo-fallback">BNY LOGO</span>
      <?php } ?>
    </div>
    <h1 class="social-login-title">Sign In</h1>
    <p class="social-login-subtitle">Login with your social account</p>

    <a class="btn-social-custom btn-facebook" href="<?php echo base_url();?>users/login_with_fb">
      <span class="btn-social-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073c0 6.025 4.388 11.021 10.125 11.927v-8.437H7.078v-3.49h3.047V9.41c0-3.017 1.792-4.684 4.533-4.684 1.313 0 2.686.236 2.686.236v2.963H15.83c-1.492 0-1.956.93-1.956 1.885v2.263h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.098 24 12.073z" fill="#fff"/>
        </svg>
      </span>
      Login with Facebook
    </a>

    <a class="btn-social-custom btn-google" href="<?php echo base_url();?>users/google_login">
      <span class="btn-social-icon" aria-hidden="true">
        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
          <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.209 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.958 3.042l5.657-5.657C34.046 6.053 29.268 4 24 4C12.954 4 4 12.954 4 24s8.954 20 20 20s20-8.954 20-20c0-1.341-.138-2.65-.389-3.917z"/>
          <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 19.006 13 24 13c3.059 0 5.842 1.154 7.958 3.042l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.343 4.337-17.694 10.691z"/>
          <path fill="#4CAF50" d="M24 44c5.165 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.14 35.091 26.715 36 24 36c-5.188 0-9.617-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
          <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.084 5.571c.001-.001 6.19 5.238 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
        </svg>
      </span>
      Sign in with Google
    </a>
  </div>
</div>
