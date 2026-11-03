<div class="container">
  <div class="form-inner-wrapper">
    <div class="form-top-section">
      <input id="passfield1" class="passfield" readonly aria-label="Generated password">
      <button id="copy-btn" class="stark-btn copy-btn">Copy Password</button>
    </div>
    <form class="form">
      <div class="form-footer-section">
        <div class="form-footer-main-column">
          <label for="password-length-range">Password Length:</label>
          <input type="range" id="password-length-range" class="password-length-range" min="1" max="40" value="20">
          <div class="length-display">20</div>
        </div>
        <div class="form-footer-column checkbox-grid">
          <label class="checkbox-wrapper">
            <input type="checkbox" id="lowercase" checked> Lowercase
          </label>
          <label class="checkbox-wrapper">
            <input type="checkbox" id="uppercase" checked> Uppercase
          </label>
          <label class="checkbox-wrapper">
            <input type="checkbox" id="numbers" checked> Numbers
          </label>
          <label class="checkbox-wrapper">
            <input type="checkbox" id="symbols"> Symbols
          </label>
        </div>
      </div>
      <div class="form-button">
        <button type="button" class="stark-btn blue-btn generate-button" id="generate_password">Generate Password</button>
      </div>
    </form>
    <div class="error-wrapper">
      <p></p>
    </div>
  </div>
</div>
