// Pisahkan inisialisasi komponen
const initThemeToggle = () => {
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;
    
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            try {
                const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                themeToggle.innerHTML = `<i class="fas fa-${newTheme === 'light' ? 'moon' : 'sun'}"></i>`;
            } catch (error) {
                console.error('Error toggling theme:', error);
            }
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', savedTheme);
        themeToggle.innerHTML = `<i class="fas fa-${savedTheme === 'light' ? 'moon' : 'sun'}"></i>`;
    }
};

const initFontSize = () => {
    const fontSizeButtons = document.querySelectorAll('.font-size-btn');
    if (fontSizeButtons.length > 0) {
        fontSizeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                try {
                    const size = e.target.dataset.size;
                    document.documentElement.style.setProperty('--content-font-size', `${size}px`);
                    localStorage.setItem('fontSize', size);
                } catch (error) {
                    console.error('Error changing font size:', error);
                }
            });
        });

        // Load saved font size
        const savedFontSize = localStorage.getItem('fontSize') || '16';
        document.documentElement.style.setProperty('--content-font-size', `${savedFontSize}px`);
    }
};

const initTTS = () => {
    const ttsButton = document.getElementById('ttsButton');
    const articleContent = document.querySelector('.article-content');
    
    if (ttsButton && articleContent) {
        let speaking = false;
        
        ttsButton.addEventListener('click', () => {
            try {
                if (!window.speechSynthesis) {
                    alert('Maaf, browser Anda tidak mendukung fitur Text-to-Speech');
                    return;
                }

                if (speaking) {
                    window.speechSynthesis.cancel();
                    speaking = false;
                    ttsButton.innerHTML = '<i class="fas fa-volume-up"></i>';
                } else {
                    const text = articleContent.textContent;
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    
                    window.speechSynthesis.speak(utterance);
                    speaking = true;
                    ttsButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    
                    utterance.onend = () => {
                        speaking = false;
                        ttsButton.innerHTML = '<i class="fas fa-volume-up"></i>';
                    };
                }
            } catch (error) {
                console.error('TTS Error:', error);
                alert('Terjadi kesalahan saat menggunakan fitur Text-to-Speech');
            }
        });
    }
};

// Initialize all components
document.addEventListener('DOMContentLoaded', function() {
    initThemeToggle();
    initFontSize();
    initTTS();
}); 