<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Health Card - {{ $patient->patient_id }}</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
        body {
        background: #e9f5f4;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        font-family: "Segoe UI", sans-serif;
      }

      .id-card {
        width: 750px;
        height: 450px;
        background: white;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        position: relative;
      }

      /* Top Banner */
      .top-banner {
        position: relative;
        height: 100px;
        background: #6e3939;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding-left: 30px;
        z-index: 2;
      }

      .top-banner::after {
        content: "";
        position: absolute;
        top: 21px;
        right: 0;
        width: 246px;
        height: 79%;
        background: #d53434;
        border-top-left-radius: 0;
        /* transform: rotate(-5deg); */
      }

      .top-banner::before {
        content: "";
        position: absolute;
        top: 7px;
        right: -48px;
        width: 200px;
        height: 109%;
        background: #ff5e5e;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        /* transform: rotate(-3deg); */
        z-index: 1;
      }

      .logo-section {
        position: relative;
        z-index: 3;
        display: flex;
        align-items: center;
      }

      .logo-section img {
        height: 50px;
        width: 50px;
        object-fit: contain;
        margin-right: 10px;
      }

      .logo-text {
        color: white;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.1;
      }

      .logo-text small {
        font-size: 12px;
        font-weight: 400;
      }

      /* Main Body */
      .card-body {
        display: flex;
        padding: 30px;
      }

      .photo {
        width: 180px;
        height: 220px;
        border: 4px solid #f34f4f;
        border-radius: 12px;
        overflow: hidden;
      }

      .photo img {
        position: relative;
        z-index: 10;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .info {
        flex-grow: 1;
        margin-left: 40px;
      }

      .info h5 {
        color: #215e61;
        font-weight: 700;
        font-size: 30px;
        margin-bottom: 5px;
      }

      .info div {
        margin-bottom: 1px;
        font-size: 14px;
      }
      .info div span {
        min-width: 188px;
        display: inline-block;
      }
      .label {
        font-weight: bold;
      }

      .barcode {
        margin-top: 10px;
      }

      .barcode img {
        height: 50px;
      }

      /* Bottom decoration */
      .card-footer-shape {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 160px;
        height: 90px;
        background: #a30000;
        border-top-right-radius: 0;

        /* Remove full border and use specific sides */
        border-top: 48px solid rgba(255, 255, 255, 0.3);
        border-right: 48px solid rgba(255, 255, 255, 0.3);

        /* Optional: ensure no other borders are applied */
        border-left: none;
        border-bottom: none;
        z-index: 2;
      }
      .address-shape {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #612121;
        border-top-right-radius: 0;
        z-index: 1;
        color: #fff;
        padding-left: 26%;
        padding-right: 15px;
        padding-top: 6px;
        padding-bottom: 6px;
        font-size: 11px;
        line-height: 1.3;
      }

      .dot-pattern {
        position: absolute;
        right: 0;
        bottom: 50%;
        width: 100px;
        height: 180px;
        background-image: radial-gradient(#f34f4f 1px, transparent 1px);
        background-size: 8px 8px;
        transform: translateY(50%);
        z-index: 0;
      }
      .dot-pattern-left {
        position: absolute;
        left: 0;
        bottom: 50%;
        width: 59px;
        height: 140px;
        background-image: radial-gradient(#f34f4f 1px, transparent 1px);
        background-size: 8px 8px;
        transform: translateY(50%);
        z-index: 0;
      }

      @media print {
        body {
          background: #ffffff;
          min-height: auto;
          margin: 0;
          padding: 0;
        }

        .id-card {
          box-shadow: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="id-card">
      <!-- Top Banner with Curves -->
      <div class="top-banner">
        <div class="logo-section">
          <img src="{{ $hospitalLogo }}" alt="Logo" onerror="this.style.display='none';" />
          <div class="logo-text">
            {{ $hospitalName }}<br /><small>HEALTH CARD</small>
          </div>
        </div>
      </div>

      <!-- Card Body -->
      <div class="card-body">
        <div class="photo">
          <img src="{{ $patient->image ? asset('public/storage/' . $patient->image) : 'https://placehold.co/180x220' }}" alt="Patient Photo" />
        </div>
        <div class="info">
          <h5>Health Card</h5>
          <div><span class="label">Name</span> : {{ $patient->name ?? '-' }}</div>
          <div><span class="label">Health ID</span> : {{ $patient->patient_id ?? '-' }}</div>
          <div><span class="label">Mobile No</span> : {{ $patient->phone ?? '-' }}</div>
          <div><span class="label">Gender</span> : {{ $patient->gender ?? '-' }}</div>
          <div><span class="label">DOB</span> : {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d-m-Y') : '-' }}</div>
          <div><span class="label">Age</span> : {{ ($patient->age_years ?? 0) . 'Y ' . ($patient->age_months ?? 0) . 'M' }}</div>
          <div class="barcode">
            <img
              src="data:image/png;base64,{{ $barcodePng }}"
              alt="Barcode"
            />
          </div>
        </div>
      </div>

      <!-- Decorative Bottom Shapes -->
      <div class="card-footer-shape"></div>
      <div class="address-shape">
        {{ $hospitalAddressLine1 }}
        @if(!empty($hospitalAddressLine2))
          <br>{{ $hospitalAddressLine2 }}
        @endif
      </div>
      <div class="dot-pattern"></div>
      <div class="dot-pattern-left"></div>
    </div>

    <script>
      (function () {
        let closeHandled = false;

        function closeAfterPrint() {
          if (closeHandled) {
            return;
          }

          closeHandled = true;
          window.close();
        }

        window.addEventListener('afterprint', closeAfterPrint);

        if (window.matchMedia) {
          const mediaQueryList = window.matchMedia('print');
          const mediaListener = function (event) {
            if (!event.matches) {
              closeAfterPrint();
            }
          };

          if (typeof mediaQueryList.addEventListener === 'function') {
            mediaQueryList.addEventListener('change', mediaListener);
          } else if (typeof mediaQueryList.addListener === 'function') {
            mediaQueryList.addListener(mediaListener);
          }
        }

        window.addEventListener('load', function () {
          window.print();
        });
      })();
    </script>
  </body>
</html>
