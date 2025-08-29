{{-- resources/views/about.blade.php --}}
@include('partials.header')

<link rel="stylesheet" href="{{ asset('css/about.css') }}"/>

<main class="site-container about">
  <!-- Hero -->
  <section class="hero">
    <h1 class="page-title">About Us</h1>
    <p class="sub">Serving comfort, speed, and finger-lickin’ good experiences—online and in-store.</p>
  </section>

  <!-- 3-up stats / highlights -->
  <section class="highlights">
    <div class="card">
      <h3>Fast Ordering</h3>
      <p>Order in seconds with a simple, secure checkout.</p>
    </div>
    <div class="card">
      <h3>Fresh & Hot</h3>
      <p>We prepare every order to deliver that just-fried crunch.</p>
    </div>
    <div class="card">
      <h3>Nationwide</h3>
      <p>Hundreds of KFC outlets across Malaysia for easy pickup.</p>
    </div>
  </section>

  <!-- Story -->
  <section class="split">
    <div class="text">
      <h2>Our Story</h2>
      <p>
        The KFC Ordering System was built to make your favorites just a tap away.
        From browsing the menu to tracking orders, our goal is a seamless, reliable,
        and fast experience — whether you’re picking up on the way home or planning
        a family meal.
      </p>
      <p>
        Behind the scenes, our team focuses on performance, accessibility, and
        constant improvements from your feedback.
      </p>
    </div>
    <div class="media">
      <div class="img-skeleton" style="background-image: url('https://media.says.com/2025/07/SAYS-COVER-2.png')"></div>
    </div>
  </section>

  <!-- Mission & Values -->
  <section class="panels">
    <article class="panel">
      <h3>Our Mission</h3>
      <p>Delight customers with smooth ordering, accurate preparation, and on-time pickup.</p>
    </article>
    <article class="panel">
      <h3>What We Value</h3>
      <ul class="values">
        <li>Reliability — your order done right, every time</li>
        <li>Speed — minimal clicks, maximum results</li>
        <li>Care — friendly support and thoughtful UX</li>
      </ul>
    </article>
    <article class="panel">
      <h3>Technology</h3>
      <p>Modern web stack, secure payments, and continuous quality monitoring for peak hours.</p>
    </article>
  </section>

  <!-- CTA -->
  <section class="cta">
    <div class="cta-card">
      <h3>Ready to order?</h3>
      <p>Browse the latest meals and deals — pickup at your nearest KFC.</p>
      <a class="btn" href="{{ route('kfc.locations') }}">Find a Location</a>
      <a class="btn secondary" href="{{ url('/menu') }}">View Menu</a>
    </div>
  </section>
</main>

@include('partials.footer')
