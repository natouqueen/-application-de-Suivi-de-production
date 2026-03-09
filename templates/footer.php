<!--  FOOTER  -->
<footer class="footer">
    <div class="footer-container">
        <p>© <?= date("Y") ?> - Huilerie | Tous droits réservés.</p>
        <p>Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['user']['nom']) ?></strong> <span class="wave"></span></p> 
    </div>
</footer>

<style>
/* FOOTER  */
.footer {
    background: linear-gradient(90deg, rgba(14, 93, 150, 1), rgba(14, 93, 150, 1));
    color: #fff;
    text-align: center;
    padding: 18px 12px;
    border-radius: 14px 14px 0 0;
    box-shadow: 0 -4px 12px rgba(0,0,0,0.2);
    margin-top: 30px;
    font-size: 0.95rem;
}

.footer-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: center;
    justify-content: center;
}

.footer p {
    margin: 0;
}

.footer .dev-info {
    font-size: 0.8rem;
    opacity: 0.85;
    font-style: italic;
}

/* Animation emoji 👋 */
.wave {
    animation: waveAnim 2s infinite;
    display: inline-block;
}
@keyframes waveAnim {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(20deg); }
    50% { transform: rotate(-15deg); }
    75% { transform: rotate(10deg); }
    100% { transform: rotate(0deg); }
}
</style>
