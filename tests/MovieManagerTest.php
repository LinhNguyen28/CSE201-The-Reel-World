<?php
use PHPUnit\Framework\TestCase;

// Load passwords
require_once('db.php');

final class MovieManagerTest extends TestCase
{
    private $testUserId = null;
    private $testRequestId = null;
    
    // Testing if MovieManager object can be created 
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            MovieManager::class,
            new MovieManager($GLOBALS['mysqli'])
        );
    }
    
    // Testing if getAllMovies() returns more than zero movies
    // Assumes there is at least 1 movie in database 
    public function testCanGetAllMovies(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getAllMovies();
        $result = $statement->store_result();
        
        $this->assertGreaterThan(
            0,
            $statement->num_rows
        );
    }
    
    // Testing if getAllMoviesByKeyword() returns movies that match keyword
    // Assumes that there is at least one movie with keyword 'man'
    public function testCanGetAllMoviesByKeyword(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getAllMoviesByKeyword('man');
        $result = $statement->store_result();
        
        $this->assertGreaterThan(
            0,
            $statement->num_rows
        );
    }
    
    // Testing if getAllMoviesByRating() returns more than zero movies
    // Assumes there is at least 1 movie in database
    public function testCanGetAllMoviesByRating(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getAllMoviesByRating();
        $result = $statement->store_result();
        
        $this->assertGreaterThan(
            0,
            $statement->num_rows
        );
    }
    
    // Testing if getAllGenres() returns all 25 genres
    // Assumes there are 24 genres in the database  
    public function testCanGetAllGenres(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getAllGenres();
        $result = $statement->store_result();
        
        $this->assertEquals(
            25,
            $statement->num_rows
        );
    }
    
    // Testing if getAllActors() returns more than 0 actors
    // Assumes there is at least 1 actor in the database   
    public function testCanGetAllActors(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getAllActors();
        $result = $statement->store_result();
        
        $this->assertGreaterThan(
            0,
            $statement->num_rows
        );
    }
    
    // Testing if getCheckedGenres() can get movies of a genre when movies of that genre exist
    // Assumes there are 5 movies with genre 'romance' 
    public function testCanGetMatchedGenres(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getCheckedGenres(array('romance'));
        $result = $statement->store_result();
        
        $this->assertEquals(
            5,
            $statement->num_rows
        );
    }
    
    // Testing if getSingleMovie() returns exactly one movie
    // Assumes there is a movie with title 'cleopatra' 
    public function testCanGetSingleMovie(): void
    {
        $mm = new MovieManager($GLOBALS['mysqli']);
        $statement = $mm->getSingleMovie('cleopatra');
        $result = $statement->store_result();
        
        $this->assertEquals(
            1,
            $statement->num_rows
        );
    }
    
    // Testing if addMovie() adds a movie to the database
    public function testCanAddMovie(): void
    {
        $this->cleanUpTest();
        $testRequestId = $this->setUpTest();
        
        $mm = new MovieManager($GLOBALS['mysqli']);
        $mm->addMovie($testRequestId, 'unittesttitle', 'unittestdescription', 'unittestlink', 'unittestimg', '1.0');
        
        $sql = "SELECT requestId FROM Movies WHERE requestId = '" . $testRequestId . "'";
        $sqlResult = $GLOBALS['mysqli']->query($sql);
        
        $this->assertGreaterThan(
            0,
            $sqlResult->num_rows
        );
        
        $row = $sqlResult->fetch_assoc();
        
        $this->assertEquals(
            $testRequestId,
            $row["requestId"]
        );
        
        $this->cleanUpTest();
    }
    
    // - - - - - HELPER FUNCTIONS - - - - - 
    // Inserts test user and test request
    // Returns request id 
    public function setUpTest(): int 
    {
        global $testUserId;
        
        // Inset test user 
        $sql = "INSERT INTO Users (userName, password, displayName) VALUES ('unittestuser', 'testpassword', 'Testfirst, Testlast')";
        
        if ($GLOBALS['mysqli']->query($sql) === TRUE) {
            $testUserId = $GLOBALS['mysqli']->insert_id;
        }
        
        // Create test request by test user 
        $sql = "INSERT INTO Requests (userId, requestName, description) VALUES ('" . $testUserId . "', 'unittestname', 'unittestdescription')";
        
        if ($GLOBALS['mysqli']->query($sql) === TRUE) {
            $requestId = $GLOBALS['mysqli']->insert_id;
        }
        
        return $requestId;
    }
    
    // Cleans up test code
    public function cleanUpTest(): void 
    {
        global $testUserId, $testRequestId; 
        
        // Delete test movie 
        $sql = "DELETE FROM Movies WHERE requestId = '" . $testRequestId . "'";
        $GLOBALS['mysqli']->query($sql);
        
        // Delete test user's requests 
        $sql = "DELETE FROM Requests WHERE userId = '" . $testUserId . "'";
        $GLOBALS['mysqli']->query($sql);
        
        // Delete test user
        $sql = "DELETE FROM Users WHERE userId = '" . $testUserId . "'";
        $GLOBALS['mysqli']->query($sql);
    }
}
?>