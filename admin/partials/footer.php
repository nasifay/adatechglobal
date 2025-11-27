</main>
  <footer id="footer" class="footer mt-5">
    <div class="footer-content position-relative">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <div class="footer-info">
              <h3>Adatech Solutions Admin</h3>
              <p>
                Addis Ababa, Ethiopia<br>
                <strong>Website:</strong> <a href="https://www.adatechglobal.com">www.adatechglobal.com</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-legal text-center position-relative">
      <div class="container">
        <div class="copyright">
          &copy; Copyright <strong><span>Adatech Solutions</span></strong>. All Rights Reserved
        </div>
      </div>
    </div>
  </footer>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
  <script>
    // Toggle collapsible header descriptions
    (function(){
      document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.collapsible').forEach(function(h){
          h.addEventListener('click', function(){
            var desc = h.nextElementSibling;
            if (!desc) return;
            var open = desc.classList.toggle('open');
            h.classList.toggle('open', open);
          });
        });
      });
    })();
  </script>
</body>
</html>
