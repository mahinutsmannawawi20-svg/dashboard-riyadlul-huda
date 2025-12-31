
<style>
    .dev-profile-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        z-index: 999;
    }
    
    .dev-profile-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .dev-profile-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
    }
    
    .dev-profile-btn svg {
        width: 24px;
        height: 24px;
        color: white;
    }
    
    .dev-profile-label {
        font-size: 0.7rem;
        color: #6366f1;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        background: white;
        padding: 4px 8px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .dev-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .dev-modal-overlay.active {
        display: flex;
    }
    
    .dev-modal {
        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        max-width: 420px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.1);
        animation: modalSlideIn 0.3s ease;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .dev-modal-header {
        position: relative;
        padding: 30px 30px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .dev-close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .dev-close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .dev-close-btn svg {
        width: 16px;
        height: 16px;
        color: #94a3b8;
    }
    
    .dev-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #6366f1;
        margin-bottom: 15px;
    }
    
    .dev-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #f8fafc;
        margin: 0 0 5px;
    }
    
    .dev-role {
        font-size: 0.9rem;
        color: #6366f1;
        font-weight: 600;
        margin: 0;
    }
    
    .dev-modal-body {
        padding: 25px 30px;
    }
    
    .dev-bio {
        font-size: 0.9rem;
        color: #94a3b8;
        line-height: 1.7;
        margin-bottom: 25px;
        text-align: justify;
    }
    
    .dev-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .dev-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        text-decoration: none;
        color: #e2e8f0;
        transition: all 0.2s ease;
    }
    
    .dev-link:hover {
        background: rgba(99, 102, 241, 0.2);
        transform: translateX(5px);
    }
    
    .dev-link-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .dev-link-icon.whatsapp { background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); }
    .dev-link-icon.email { background: linear-gradient(135deg, #ea4335 0%, #c5221f 100%); }
    .dev-link-icon.github { background: linear-gradient(135deg, #333 0%, #24292e 100%); }
    .dev-link-icon.website { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    
    .dev-link-icon svg {
        width: 20px;
        height: 20px;
        color: white;
    }
    
    .dev-link-text {
        flex: 1;
    }
    
    .dev-link-label {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 2px;
    }
    
    .dev-link-value {
        font-size: 0.9rem;
        color: #e2e8f0;
    }
    
    .dev-modal-footer {
        padding: 20px 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }
    
    .dev-company {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #64748b;
    }
    
    .dev-company img {
        height: 20px;
    }
    
    @media (max-width: 768px) {
        .dev-profile-container {
            bottom: 15px;
            right: 15px;
        }
        
        .dev-profile-btn {
            width: 45px;
            height: 45px;
        }
        
        .dev-profile-label {
            font-size: 0.65rem;
        }
        
        .dev-modal {
            margin: 10px;
        }
        
        .dev-modal-header, .dev-modal-body, .dev-modal-footer {
            padding-left: 20px;
            padding-right: 20px;
        }
    }
</style>


<div class="dev-profile-container">
    <button class="dev-profile-btn" onclick="openDevModal()" title="Hubungi Admin">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
    </button>
    <span class="dev-profile-label">Hubungi Admin</span>
</div>


<div class="dev-modal-overlay" id="devModalOverlay" onclick="closeDevModal(event)">
    <div class="dev-modal" onclick="event.stopPropagation()">
        <div class="dev-modal-header">
            <button class="dev-close-btn" onclick="closeDevModal()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <img src="<?php echo e(asset('images/developer-photo.jpg')); ?>" alt="Developer Photo" class="dev-photo">
            <h3 class="dev-name">Mahin Utsman Nawawi, S.H.</h3>
            <p class="dev-role">Founder & CEO - Velora</p>
        </div>
        
        <div class="dev-modal-body">
            <p class="dev-bio">
                Seorang Sarjana Hukum yang memiliki passion kuat di bidang teknologi dan pengembangan web. 
                Kombinasi unik antara latar belakang hukum dan keahlian teknis memberikan perspektif holistik 
                dalam membangun solusi digital yang tidak hanya canggih, tapi juga aman dan sesuai regulasi.
            </p>
            
            <div class="dev-links">
                <a href="https://wa.me/6281320442174" target="_blank" class="dev-link">
                    <div class="dev-link-icon whatsapp">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div class="dev-link-text">
                        <div class="dev-link-label">WhatsApp</div>
                        <div class="dev-link-value">+62 813-2044-2174</div>
                    </div>
                </a>
                
                <a href="mailto:nawawimahinutsman@gmail.com" class="dev-link">
                    <div class="dev-link-icon email">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="dev-link-text">
                        <div class="dev-link-label">Email</div>
                        <div class="dev-link-value">nawawimahinutsman@gmail.com</div>
                    </div>
                </a>
                
                <a href="https://github.com/mahinutsmannawawi20-svg" target="_blank" class="dev-link">
                    <div class="dev-link-icon github">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </div>
                    <div class="dev-link-text">
                        <div class="dev-link-label">GitHub</div>
                        <div class="dev-link-value">mahinutsmannawawi20-svg</div>
                    </div>
                </a>
                
                <a href="https://www.ve-lora.my.id/" target="_blank" class="dev-link">
                    <div class="dev-link-icon website">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                    </div>
                    <div class="dev-link-text">
                        <div class="dev-link-label">Website</div>
                        <div class="dev-link-value">ve-lora.my.id</div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="dev-modal-footer">
            <div class="dev-company">
                <span>Powered by</span>
                <strong style="color: #8b5cf6;">Velora</strong>
            </div>
        </div>
    </div>
</div>

<script>
    function openDevModal() {
        document.getElementById('devModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDevModal(event) {
        if (!event || event.target === document.getElementById('devModalOverlay')) {
            document.getElementById('devModalOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDevModal();
        }
    });
</script>
<?php /**PATH C:\Users\v\.gemini\antigravity\scratch\dashboard-riyadlul-huda\resources\views\components\developer-profile.blade.php ENDPATH**/ ?>