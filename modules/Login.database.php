<?php

/*
 * authenticate users against database users
 */

function halon_login_database($username, $password, $method, $settings)
{
	$dbh = $settings->getDatabase();
	$statement = $dbh->prepare("SELECT * FROM users WHERE username = :username;");
	if (!$statement->execute(array(':username' => $username)))
		return false;

	$row = $statement->fetch(PDO::FETCH_ASSOC);
	if (!$row || !password_verify($password, $row['password']))
		return false;

	$statement = $dbh->prepare("SELECT * FROM users_relations WHERE username = :username;");
	if (!$statement->execute(array(':username' => $row['username'])))
		return false;

	$result = array();
	$result['username'] = $row['username'];
	$result['source'] = 'database';
	$result['access'] = array();

	while ($row = $statement->fetch(PDO::FETCH_ASSOC))
		$result['access'][$row['type']][] = $row['access'];

	return $result;
}
