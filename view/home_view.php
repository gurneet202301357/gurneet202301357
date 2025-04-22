<main>
    <h1>Twixitter</h1>
    <?php echo $postsList; ?>

    <?php if (isset($is_authenticated) && $is_authenticated): ?>
        <h2>Reactions</h2>
        <ul>
            <li><a href="twixitter.php?action=react&reaction=love&post_id=<?php echo $post_id; ?>">Love</a></li>
            <li><a href="twixitter.php?action=react&reaction=haha&post_id=<?php echo $post_id; ?>">Haha</a></li>
            <li><a href="twixitter.php?action=react&reaction=sad&post_id=<?php echo $post_id; ?>">Sad</a></li>
            <li><a href="twixitter.php?action=react&reaction=angry&post_id=<?php echo $post_id; ?>">Angry</a></li>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>
