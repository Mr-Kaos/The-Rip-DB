<?php

namespace RipDB;

require_once('Model.php');

class RipModel extends Model
{
	const TABLE = 'Rips';

	public function getRip(int $id)
	{
		$qry = $this->db->table(self::TABLE)
			->columns('RipID', 'RipName', 'RipDate', 'RipAlternateName', 'RipLength', 'RipURL', 'RipDescription', 'GameID', 'GameName', 'ChannelName', 'ChannelURL')
			->eq('RipID', $id)
			->join('Games', 'GameID', 'RipGame')
			->join('Channels', 'ChannelID', 'RipChannel');

		// Get jokes and rips from the resultset of rips.
		$ripJokes = $this->getRipJokes($qry);
		$rippers = $this->getRipRippers($qry);

		$rip = $qry->findOne();

		// Apply jokes to rips
		foreach ($ripJokes as $joke) {
			if (!isset($rip['Jokes'])) {
				$rip['Jokes'] = [];
			}

			$rip['Jokes'][$joke['JokeID']] = $joke;
		}

		// Apply rippers to rips
		foreach ($rippers as $ripper) {
			if (!isset($rip['Rippers'])) {
				$rip['Rippers'] = [];
			}

			$rip['Rippers'][$ripper['RipperID']] = $ripper;
		}
		return $rip;
	}

	/**
	 * Finds all jokes that are contained in the given resultset of rips.
	 * @return A 2D array where each key is the ID of the joke and the values are the columns from the Jokes Table, along with its tags.
	 */
	private function getRipJokes($ripQuery)
	{
		$qry = $this->db->table('Jokes')
			->columns('r.RipID, RipJokes.JokeID', 'JokeName', 'JokeTimestamps', 'JokeComment')
			->join('RipJokes', 'JokeID', 'JokeID')
			->joinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipJokes');

		$tags = $this->getJokeTags($qry);
		$jokes = $qry->findAll();

		$tags = $this->setSubArrayValueToKey2D($tags, 'JokeID');

		foreach ($jokes as &$joke) {
			$joke['Tags'] = [];
			foreach ($tags[$joke['JokeID']] as $tag) {
				$joke['Tags'][$tag['TagID']] = $tag['TagName'];
			}
		}

		return $jokes;
	}

	private function getJokeTags($qry)
	{
		return $this->db->table('Tags')
			->columns('j.JokeID', 'JokeTags.TagID', 'TagName')
			->join('JokeTags', 'TagID', 'TagID')
			->joinSubquery($qry, 'j', 'JokeID', 'JokeID', 'JokeTags')
			->findAll();
	}

	private function getRipRippers($ripQuery)
	{
		$qry = $this->db->table('Rippers')
			->columns('r.RipID, RipRippers.RipperID', 'RipperName')
			->join('RipRippers', 'RipperID', 'RipperID')
			->joinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipRippers');

		return $qry->findAll();
	}
}
