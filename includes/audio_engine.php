<?php
// includes/audio_engine.php
// Este script se inyecta silenciosamente en el HTML y provee el API de Audio Global
?>
<script>
    const AudioManager = {
        // Carga el estado guardado por el usuario (mutado o no)
        muted: localStorage.getItem('app_muted') === 'true',
        
        // Instancias de Audio
        bgm: new Audio('assets/audio/bgm.mp3'),
        sounds: {
            correct: new Audio('assets/audio/correct.mp3'),
            wrong: new Audio('assets/audio/wrong.mp3'),
            win: new Audio('assets/audio/win.mp3'),
            pop: new Audio('assets/audio/pop.mp3') // Para clics o tocar botones
        },

        init: function() {
            // Configurar música de fondo (Loop infinito, volumen bajo)
            this.bgm.loop = true;
            this.bgm.volume = 0.15; // 15% para que no opaque los efectos
            
            // Configurar volumen de efectos (70% para que destaquen)
            Object.values(this.sounds).forEach(s => s.volume = 0.7);

            this.applyMute();

            // Ciberseguridad/UX: Los navegadores bloquean el audio si no hay interacción previa.
            // Escuchamos el primer click o tecla para iniciar la música de fondo.
            const startAudioContext = () => {
                if (!this.muted) {
                    this.bgm.play().catch(e => console.warn("Autoplay bloqueado por el navegador", e));
                }
                // Una vez iniciado, removemos los listeners para no saturar memoria
                document.removeEventListener('pointerdown', startAudioContext);
                document.removeEventListener('keydown', startAudioContext);
            };

            document.addEventListener('pointerdown', startAudioContext);
            document.addEventListener('keydown', startAudioContext);
            
            // Sincronizar el ícono visual del botón
            this.updateUI();
        },

        playSound: function(type) {
            if (this.muted || !this.sounds[type]) return;
            
            // Clonamos el nodo de audio. Esto es VITAL porque si el niño acierta 
            // 2 veces rápido, el sonido debe superponerse, no reiniciarse bruscamente.
            let soundClone = this.sounds[type].cloneNode();
            soundClone.volume = this.sounds[type].volume;
            soundClone.play().catch(e => console.warn("Error al reproducir efecto", e));
        },

        toggleMute: function() {
            this.muted = !this.muted;
            localStorage.setItem('app_muted', this.muted); // Guardar preferencia en el dispositivo
            this.applyMute();
            this.updateUI();
        },

        applyMute: function() {
            if (this.muted) {
                this.bgm.pause();
            } else {
                // Solo intentar reproducir si el navegador ya permite audio
                if (this.bgm.readyState >= 2) {
                    this.bgm.play().catch(e => console.warn("Esperando interacción", e));
                }
            }
        },

        updateUI: function() {
            // Busca el botón que pusimos en lesson.php
            const btn = document.getElementById('music-toggle');
            if (btn) {
                btn.innerText = this.muted ? '🔇' : '🔊';
                // Añadir un pequeño efecto visual al cambiar
                btn.style.transform = 'scale(1.2)';
                setTimeout(() => btn.style.transform = 'scale(1)', 200);
            }
        }
    };

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        AudioManager.init();
    });

    // Función global llamada por el botón "onclick" en HTML
    function toggleMusic() {
        AudioManager.toggleMute();
    }
</script>