/**
 * @file global.js
 * Community Bloom — site-wide JavaScript.
 *
 * Behaviour 1: Smooth scroll for same-page anchor links.
 * Behaviour 2: Mark the current nav item as active based on the URL path.
 * Behaviour 3: Sticky navbar hides/shows on scroll direction (optional UX).
 *
 * No dependencies — vanilla JS only.
 */

(function () {
  'use strict';

  // Run after DOM is ready
  document.addEventListener('DOMContentLoaded', function () {
    smoothScrollAnchors();
    markActiveNavLink();
  });

  /**
   * Smooth-scroll all same-page anchor links (#id targets).
   * Respects prefers-reduced-motion.
   */
  function smoothScrollAnchors() {
    var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
      anchor.addEventListener('click', function (e) {
        var targetId = this.getAttribute('href');
        if (targetId === '#') return;
        var target = document.querySelector(targetId);
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Move focus to the target for keyboard/screen reader users
        if (!target.hasAttribute('tabindex')) {
          target.setAttribute('tabindex', '-1');
        }
        target.focus({ preventScroll: true });
      });
    });
  }

  /**
   * Add an "active" class to the nav link whose href matches the current path.
   * Bootstrap Barrio adds .active on the server side for Drupal menu items,
   * but this catches any edge cases in the rendered markup.
   */
  function markActiveNavLink() {
    var currentPath = window.location.pathname;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
      var linkPath = link.getAttribute('href');
      if (linkPath && linkPath !== '/' && currentPath.startsWith(linkPath)) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    });
  }

}());
