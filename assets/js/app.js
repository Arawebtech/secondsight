 const contentData = {
      mission: `At <span class="highlight">Second Sight Foundation</span>, our mission is to bridge the profound gap between science and spirituality. We strive to empower individuals to awaken their inner light, unlock their true potential, and embrace the harmony of both reason and intuition.`,

      vision: `Our vision is to create a global community where individuals are spiritually enlightened and scientifically aware. We envision a world where people live consciously, guided by inner wisdom and outer knowledge, in alignment with their highest selves.`,

      values: `Our core values include <span class="highlight">compassion</span>, <span class="highlight">integrity</span>, and <span class="highlight">growth</span>. We are committed to nurturing the human spirit, encouraging curiosity, and supporting transformation through conscious living.`
    };

    function showTab(tab, element) {
      document.getElementById('content').innerHTML = contentData[tab];
      document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
      element.classList.add('active');
    }

    // faq
      const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    question.addEventListener('click', () => {
      item.classList.toggle('active');

      // Close other open FAQs
      faqItems.forEach(otherItem => {
        if (otherItem !== item) {
          otherItem.classList.remove('active');
        }
      });
    });
  });

  // customer review
  