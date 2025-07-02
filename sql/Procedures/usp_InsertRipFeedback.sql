-- Stored procedure for inserting feedback about a rip.
-- Used mainly by the rip guesser game.

-- Only a Upvote or JokeFeedback should be given.
-- That is, if submitting an upvote/downvote, the "JokeFeedback" parameter should be NULL. If submitting joke feedback, the "Upvote" parameter should be null.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertRipFeedback;

CREATE PROCEDURE RipDB.usp_InsertRipFeedback(
	IN RipIDIn int,
	IN Upvote int, -- must be a 1 or 0 (bit). Not set to BIT as PicoDB does not like them for now...
	IN JokeFeedback varchar(1024)
)
BEGIN

	-- If an upvote/dwnvote is given, update the rip's upvote/downvote count
	IF (Upvote IS NOT NULL) THEN

		-- Check if a record exists first. If it does not, insert one. ELse, update the existing record.
		IF (SELECT RipID FROM RipGuesserUpvotes WHERE RipID = RipIDIn) IS NULL THEN
			INSERT INTO RipGuesserUpvotes
				(RipID, Upvotes, Downvotes)
			VALUES
				(RipIDIn, IF(Upvote => 1, 1, 0), IF(Upvote => 1, 0, 1));
		ELSE
			UPDATE RipGuesserUpvotes
			SET Upvotes = IF(Upvote => 1, Upvotes + 1, Upvotes),
				Downvotes = IF(Upvote => 1, Downvotes, Downvotes + 1);
		END IF;
	-- If a joke feedback is given, submit a new feedback record.
	ELSE
		INSERT INTO RipJokeFeedback
			(RipID, JokeFeedback)
		VALUES
			(RipIDIn, JokeFeedback);
	END IF;

	COMMIT;
END
