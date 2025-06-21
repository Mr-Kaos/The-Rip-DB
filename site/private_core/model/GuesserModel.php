<?php

namespace RipDB\Model;

require_once('Model.php');

class GuesserModel extends Model
{
	const TABLE = 'Rips';

	public function initGame($settings): bool
	{
		$success = true;
		$gameID = uniqid("", true);
		try {
			$this->db->execute("CALL usp_NewRipGuesserGame(?, ?)", [$gameID, serialize($settings)]);
		} catch (\PicoDb\SQLException $error) {
			error_log($error->getMessage());
			$success = false;
		}
		return $success;
	}
}
