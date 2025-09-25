<!-- components/daily_insight.php -->
<div class="bg-black/20 p-6 rounded-lg">
    <!-- Images -->
    
    <img src="<?= isset($dailyImage['url']) ? htmlspecialchars($dailyImage['url']) : 'https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D' ?>" 
                alt="<?= isset($dailyImage['alt_text']) ? htmlspecialchars($dailyImage['alt_text']) : 'Random inspirational photo' ?>" 
                class="w-full h-65 object-cover rounded-md mb-6" id="random-photo">
    
    <!-- Daily Quote -->
    <div class="bg-darkbg p-4 rounded-md border-l-4 border-secondary">
        <h3 class="font-dunkin text-lg text-secondary mb-2">Quote of the Day</h3>
        <blockquote class="font-cloudyday text-xl text-ghost italic">
            "<?= isset($dailyQuote['quote']) ? htmlspecialchars($dailyQuote['quote']) : 'No quote available' ?>"
        </blockquote>
        <cite class="block text-right mt-2 font-porky text-accent">- <?= htmlspecialchars($dailyQuote['author']) ?></cite>
    </div>
</div>