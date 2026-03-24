/* ============================================================
   BatiPro Construction - script.js
   JavaScript vanilla pour toutes les interactions
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
  // ---- Navbar scroll effect ----
  const navbar = document.getElementById('navbar');
  const handleScroll = () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', handleScroll);
  handleScroll();

  // ---- Mobile menu toggle ----
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const menuIconOpen = document.getElementById('menu-icon-open');
  const menuIconClose = document.getElementById('menu-icon-close');
  let menuOpen = false;

  mobileMenuBtn.addEventListener('click', () => {
    menuOpen = !menuOpen;
    if (menuOpen) {
      mobileMenu.classList.add('open');
      menuIconOpen.style.display = 'none';
      menuIconClose.style.display = 'block';
      mobileMenuBtn.setAttribute('aria-label', 'Fermer le menu');
    } else {
      mobileMenu.classList.remove('open');
      menuIconOpen.style.display = 'block';
      menuIconClose.style.display = 'none';
      mobileMenuBtn.setAttribute('aria-label', 'Ouvrir le menu');
    }
  });

  // Close mobile menu on link click
  const mobileMenuLinks = mobileMenu.querySelectorAll('a');
  mobileMenuLinks.forEach(link => {
    link.addEventListener('click', () => {
      menuOpen = false;
      mobileMenu.classList.remove('open');
      menuIconOpen.style.display = 'block';
      menuIconClose.style.display = 'none';
      mobileMenuBtn.setAttribute('aria-label', 'Ouvrir le menu');
    });
  });

  // ---- Scroll-triggered animations (IntersectionObserver) ----
  const animatedElements = document.querySelectorAll('[data-animate]');
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = entry.target.getAttribute('data-delay') || '0';
        entry.target.style.animationDelay = delay + 's';
        entry.target.classList.add('animate-fade-in-up');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  animatedElements.forEach(el => {
    el.classList.add('animate-hidden');
    observer.observe(el);
  });

  // ---- Contact form submission (envoi vers PHP/MySQL) ----
  const contactForm = document.getElementById('contact-form');
  const formSuccess = document.getElementById('form-success');
  const formError = document.getElementById('form-error');

  if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Masquer les messages precedents
      if (formError) formError.classList.remove('show');

      // Recuperer les donnees du formulaire
      const formData = {
        prenom:    document.getElementById('firstName').value,
        nom:       document.getElementById('lastName').value,
        email:     document.getElementById('email').value,
        telephone: document.getElementById('phone').value,
        service:   document.getElementById('service').value,
        message:   document.getElementById('message').value
      };

      // Desactiver le bouton pendant l'envoi
      const submitBtn = contactForm.querySelector('.btn-submit');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Envoi en cours...';

      try {
        const response = await fetch('submit_contact.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
          contactForm.style.display = 'none';
          formSuccess.classList.add('show');

          setTimeout(() => {
            formSuccess.classList.remove('show');
            contactForm.style.display = 'block';
            contactForm.reset();
          }, 5000);
        } else {
          // Afficher les erreurs de validation
          const errorMsg = result.errors
            ? result.errors.join(' ')
            : (result.error || 'Une erreur est survenue.');
          if (formError) {
            formError.textContent = errorMsg;
            formError.classList.add('show');
          } else {
            alert(errorMsg);
          }
        }
      } catch (err) {
        // Erreur reseau ou serveur indisponible
        const errorMsg = 'Impossible de contacter le serveur. Verifiez votre connexion ou reessayez plus tard.';
        if (formError) {
          formError.textContent = errorMsg;
          formError.classList.add('show');
        } else {
          alert(errorMsg);
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  // ---- Newsletter form (envoi vers PHP/MySQL) ----
  const newsletterForm = document.getElementById('newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const input = newsletterForm.querySelector('input');
      const btn = newsletterForm.querySelector('button');
      const originalText = btn.textContent;

      btn.disabled = true;
      btn.textContent = 'Envoi...';

      try {
        const response = await fetch('submit_newsletter.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: input.value })
        });

        const result = await response.json();
        alert(result.message || result.error || 'Merci pour votre inscription !');
        if (result.success) input.value = '';
      } catch (err) {
        alert('Impossible de contacter le serveur. Reessayez plus tard.');
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });
  }
});
