<?php
// includes/audio_engine.php
// Este script se inyecta silenciosamente en el HTML y provee el API de Audio Global
?>
<script>
    const AudioManager = {
        // Inicializa silenciado por defecto para no romper las reglas del navegador
        muted: true,
        
        // Instancias de Audio
        sounds: {
            correct: new Audio('assets/audio/correct.mp3'),
            wrong: new Audio('assets/audio/wrong.mp3'),
            win: new Audio('assets/audio/win.mp3'),
            pop: new Audio('assets/audio/pop.mp3') // Para clics o tocar botones
        },

        init: function() {
            // Sincronizar el estado de silencio con la preferencia global de la app al cargar
            this.muted = localStorage.getItem('mw_music_pref') === 'false';
            
            // Forzar el volumen de efectos al 100% y precargarlos
            Object.values(this.sounds).forEach(s => {
                s.volume = 1.0;
                s.load(); 
            });
        },

        playSound: function(type) {
            if (this.muted || !this.sounds[type]) return;
            
            try {
                // FIX: Compatible con móviles y Safari. 
                // Pausamos y reiniciamos el tiempo en lugar de usar cloneNode()
                this.sounds[type].pause();
                this.sounds[type].currentTime = 0;
                
                let playPromise = this.sounds[type].play();
                if (playPromise !== undefined) {
                    playPromise.catch(e => console.warn("Efecto de audio bloqueado por el navegador:", e));
                }
            } catch (e) {
                console.warn("Error reproduciendo el efecto:", e);
            }
        }
    };

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        AudioManager.init();
    });
</script>