<?php
// controller.php
// Controller for handling reactions in Twixitter

// Database connection
$dsn = 'mysql:host=localhost;dbname=twixitter';
$dbuser = 'root';
$dbpass = '';

try {
    $db = new PDO($dsn, $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Add a reaction to a post
function addReaction($post_id, $account_id, $reaction)
{
    global $db;

    $query = 'SELECT * FROM reactions WHERE post_id = :post_id AND account_id = :account_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':post_id', $post_id);
    $statement->bindValue(':account_id', $account_id);
    $statement->execute();
    $existing_reaction = $statement->fetch();
    $statement->closeCursor();

    if ($existing_reaction) {
        $query = 'DELETE FROM reactions WHERE post_id = :post_id AND account_id = :account_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':post_id', $post_id);
        $statement->bindValue(':account_id', $account_id);
        $statement->execute();
        $statement->closeCursor();
    } else {
        $query = 'INSERT INTO reactions (post_id, account_id, reaction) VALUES (:post_id, :account_id, :reaction)';
        $statement = $db->prepare($query);
        $statement->bindValue(':post_id', $post_id);
        $statement->bindValue(':account_id', $account_id);
        $statement->bindValue(':reaction', $reaction);
        $statement->execute();
        $statement->closeCursor();
    }
}

// Get reactions for a specific post
function getReactions($post_id)
{
    global $db;

    $query = 'SELECT reaction, COUNT(*) AS count FROM reactions WHERE post_id = :post_id GROUP BY reaction';
    $statement = $db->prepare($query);
    $statement->bindValue(':post_id', $post_id);
    $statement->execute();
    $reactions = $statement->fetchAll();
    $statement->closeCursor();

    return $reactions;
}

// Check if a user has reacted to a post
function userHasReacted($post_id, $account_id)
{
    global $db;

    $query = 'SELECT * FROM reactions WHERE post_id = :post_id AND account_id = :account_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':post_id', $post_id);
    $statement->bindValue(':account_id', $account_id);
    $statement->execute();
    $reaction = $statement->fetch();
    $statement->closeCursor();

    return $reaction !== false;
}
?>
