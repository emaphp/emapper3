<?php
namespace eMapper\SQLite;

use eMapper\SQLite\SQLiteConfig;
use eMapper\MapperTest;
use Acme\Storage\User;
use Acme\Storage\Profile;
use eMapper\Query\Attr;
use Acme\Storage\Client;
use Acme\Storage\Pet;
use Acme\Storage\Driver;
use Acme\Storage\Car;
use Acme\Storage\Task;
use Acme\Storage\Employee;
use Acme\Storage\Person;
use Acme\Storage\Address;

/**
 * 
 * @author emaphp
 * @group storage
 */
class StorageTest extends MapperTest {
	use SQLiteConfig;
	
	protected function getFilename() {
		return __DIR__ . '/storage.db';
	}
		
	protected function truncateTable($table) {
		$mapper = $this->getMapper();
		$mapper->newQuery()->deleteFrom($table)->exec();
		$mapper->close();
	}
	
	public function testSave() {
		$this->truncateTable('users');
		$login = new \Datetime;
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		
		//entity
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@github.com';
		
		$id = $usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//stdclass
		$user = new \stdClass();
		$user->name = 'jdoe';
		$user->lastLogin = $login;
		$user->email = 'jdoe@github.com';
		
		$id = $usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		//array
		$user = [
			'name' => 'jarc',
			'lastLogin' => $login,
			'email' => 'jarc@github.com'
		];
		
		$id = $usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user['id']);
		
		$mapper->close();
	}
	
	public function testDuplicate() {
		$this->truncateTable('users');
		$login = new \Datetime;
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@github.com';
		
		$id = $usersManager->save($user);
		$this->assertInternalType('integer', $id);
		$this->assertEquals($id, $user->id);
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = $login;
		$user->email = 'emaphp@twitter.com';
		
		$newid = $usersManager->save($user);
		$this->assertInternalType('integer', $newid);
		$this->assertEquals($newid, $user->id);
		$this->assertEquals($newid, $id);
		
		//
		$newuser = $usersManager->findByPk($newid);
		$this->assertEquals($newuser->email, 'emaphp@twitter.com');
		
		$mapper->close();
		
		//TODO: for some reason database does not update the row
	}
	
	/*
	 * ONE-TO-ONE
	 */
	
	public function testOneToOneEmpty() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
		
		$userId = $usersManager->save($user, 0);
		$count = $profilesManager->count();
		$this->assertEquals(0, $count);
		
		$mapper->close();
	}
	
	public function testOneToOneUser() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
	
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
	
		$userId = $usersManager->save($user);
		$profile = $profilesManager->get(Attr::userId()->eq($userId));
		$this->assertInstanceOf('Acme\Storage\Profile', $profile);
		$this->assertEquals($profile->userId, $userId);
	
		$mapper->close();
	}
	
	public function testOneToOneProfile() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
	
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$profile->user = $user;
		
		$profileId = $profilesManager->save($profile);
		$this->assertNotNull($profile->user->id);
	
		$mapper->close();
	}
	
	public function testOneToOneDeleteUser() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
	
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
	
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$user->profile = $profile;
	
		$userId = $usersManager->save($user);
		$this->assertEquals(1, $profilesManager->count());
		$usersManager->delete($user);
		$this->assertEquals(0, $profilesManager->count());
	
		$mapper->close();
	}
	
	public function testOneToOneDeleteProfile() {
		$this->truncateTable('users');
		$this->truncateTable('profiles');
		
		$mapper = $this->getMapper();
		$usersManager = $mapper->newManager('Acme\Storage\User');
		$profilesManager = $mapper->newManager('Acme\Storage\Profile');
		
		$user = new User();
		$user->name = 'emaphp';
		$user->lastLogin = new \DateTime();
		$user->email = 'emaphp@github.com';
		
		$profile = new Profile();
		$profile->firstname = 'Emmanuel';
		$profile->lastname = 'Antico';
		$profile->gender = 'M';
		$profile->user = $user;
		
		$profileId = $profilesManager->save($profile);
		$this->assertEquals(1, $usersManager->count());
		$profilesManager->delete($profile);
		$this->assertEquals(0, $profilesManager->count());
		$this->assertEquals(1, $usersManager->count());
		$mapper->close();
	}
	
	public function testOneToOneDeleteAddress() {
		$this->truncateTable('people');
		$this->truncateTable('addresses');
		
		$mapper = $this->getMapper();
		$personsManager = $mapper->newManager('Acme\Storage\Person');
		$addressManager = $mapper->newManager('Acme\Storage\Address');
		
		$person = new Person();
		$person->name = 'Lazaro';
		$person->lastname = 'Baez';
		
		$address = new Address();
		$address->street = 'Balcarce';
		$address->number = 54;
		$address->city = 'Capital Federal';
		
		$person->address = $address;
		
		$personId = $personsManager->save($person);
		$this->assertInternalType('integer', $personId);
		$this->assertEquals(1, $addressManager->count());
		$addressManager->delete($address);
		$this->assertEquals(0, $addressManager->count());
		$person = $personsManager->findByPk($personId);
		$this->assertNull($person->addressId);
		$this->assertNull($person->address);
		
		$mapper->close();
	}
	
	/*
	 * ONE-TO_MANY
	 */
	public function testOneToManyEmpty() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$clientsManager = $mapper->newManager('Acme\Storage\Client');
		$petsManager = $mapper->newManager('Acme\Storage\Pet');
		
		$pet = new Pet();
		$pet->name = 'Pichu';
		$pet->type = 'dog';
		
		$client = new Client();
		$client->firstname = 'Joe';
		$client->lastname = 'Doe';
		$client->pets = [
			$pet
		];
		
		$clientsManager->save($client, 0);
		$this->assertEquals(0, $petsManager->count());
		$mapper->close();
	}
	
	public function testOneToManyClient() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$clientsManager = $mapper->newManager('Acme\Storage\Client');
		$petsManager = $mapper->newManager('Acme\Storage\Pet');
	
		$pet1 = new Pet();
		$pet1->name = 'Pichu';
		$pet1->type = 'dog';
		
		$pet2 = new Pet();
		$pet2->name = 'Michu';
		$pet2->type = 'cat';
	
		$client = new Client();
		$client->firstname = 'Joe';
		$client->lastname = 'Doe';
		$client->pets = [
			$pet1,
			$pet2
		];
	
		$clientsManager->save($client);
		$this->assertEquals(2, $petsManager->count());
		$mapper->close();
	}
	
	public function testOneToManyPet() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$petsManager = $mapper->newManager('Acme\Storage\Pet');
		$clientsManager = $mapper->newManager('Acme\Storage\Client');
		
		$pet = new Pet();
		$pet->name = 'Pichu';
		$pet->type = 'dog';
		
		$client = new Client();
		$client->firstname = 'Joe';
		$client->lastname = 'Doe';
		
		$pet->owner = $client;
		
		$petsManager->save($pet);
		$this->assertNotNull($pet->owner->id);
		$this->assertEquals(1, $clientsManager->count());
		
		$mapper->close();
	}
	
	public function testOneToManyDeleteClient() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
	
		$mapper = $this->getMapper();
		$clientsManager = $mapper->newManager('Acme\Storage\Client');
		$petsManager = $mapper->newManager('Acme\Storage\Pet');
	
		$pet1 = new Pet();
		$pet1->name = 'Pichu';
		$pet1->type = 'dog';
	
		$pet2 = new Pet();
		$pet2->name = 'Michu';
		$pet2->type = 'cat';
	
		$client1 = new Client();
		$client1->firstname = 'Joe';
		$client1->lastname = 'Doe';
		$client1->pets = [
			$pet1
		];
		
		$client2 = new Client();
		$client2->firstname = 'Jane';
		$client2->lastname = 'Doe';
		$client2->pets = [
			$pet2
		];
	
		$clientsManager->save($client1);
		$clientsManager->save($client2);
		$this->assertEquals(2, $clientsManager->count());
		$this->assertEquals(2, $petsManager->count());
		$clientsManager->delete($client2);
		$this->assertEquals(1, $petsManager->count());
		
		$mapper->close();
	}
	
	public function testOneToManyDeletePet() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
	
		$mapper = $this->getMapper();
		$petsManager = $mapper->newManager('Acme\Storage\Pet');
		$clientsManager = $mapper->newManager('Acme\Storage\Client');
	
		$pet = new Pet();
		$pet->name = 'Pichu';
		$pet->type = 'dog';
	
		$client = new Client();
		$client->firstname = 'Joe';
		$client->lastname = 'Doe';
	
		$pet->owner = $client;
	
		$petsManager->save($pet);
		$this->assertEquals(1, $clientsManager->count());
		$petsManager->delete($pet);
		$this->assertEquals(1, $clientsManager->count());
	
		$mapper->close();
	}
	
	public function testOneToManyDeleteDriver() {
		$this->truncateTable('drivers');
		$this->truncateTable('cars');
		
		$mapper = $this->getMapper();
		$driversManager = $mapper->newManager('Acme\Storage\Driver');
		$carsManager = $mapper->newManager('Acme\Storage\Car');
		
		$driver = new Driver();
		$driver->name = 'Jake';
		$driver->birthDate = '1978-06-22';
		
		$car = new Car();
		$car->brand = 'Ford';
		$car->model = 'Fiesta';
		
		$driver->cars = [$car];
		$driversManager->save($driver);
		$this->assertEquals(1, $carsManager->count());
		$driversManager->delete($driver);
		$this->assertEquals(1, $carsManager->count());
		$car = $carsManager->get();
		$this->assertNull($car->driverId);
		
		$mapper->close();
	}
	
	public function testManyToMany() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$employeesManager = $mapper->newManager('Acme\Storage\Employee');
		$tasksManager = $mapper->newManager('Acme\Storage\Task');
		
		$task1 = new Task();
		$task1->name = 'Task 1';
		$task1->started = true;
		$task1->startingDate = new \Datetime;
		
		$task2 = new Task();
		$task2->name = 'Task 2';
		$task2->started = false;
		$task2->startingDate = new \Datetime;
		
		$task3 = new Task();
		$task3->name = 'Task 3';
		$task3->started = true;
		$task3->startingDate = new \Datetime;
		
		$emp1 = new Employee();
		$emp1->firstname = 'Joe';
		$emp1->lastname = 'Doe';
		$emp1->department = 'Sales';
		$emp1->tasks = [$task1, $task2];
		
		$emp2 = new Employee();
		$emp2->firstname = 'Jane';
		$emp2->lastname = 'Doe';
		$emp2->department = 'IT';
		$emp2->tasks = [$task1, $task3];
		
		$employeesManager->save($emp1);
		$employeesManager->save($emp2);
		
		$totalTasks = $tasksManager->count();
		$this->assertEquals(3, $totalTasks);
		
		$related = $mapper->type('i')->query("SELECT COUNT(*) FROM emp_tasks");
		$this->assertEquals(4, $related);
		
		$mapper->close();
	}
	
	public function testManyToManyDeleteTask() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
	
		$mapper = $this->getMapper();
		$employeesManager = $mapper->newManager('Acme\Storage\Employee');
		$tasksManager = $mapper->newManager('Acme\Storage\Task');
	
		$task1 = new Task();
		$task1->name = 'Task 1';
		$task1->started = true;
		$task1->startingDate = new \Datetime;
	
		$task2 = new Task();
		$task2->name = 'Task 2';
		$task2->started = false;
		$task2->startingDate = new \Datetime;
	
		$task3 = new Task();
		$task3->name = 'Task 3';
		$task3->started = true;
		$task3->startingDate = new \Datetime;
	
		$emp1 = new Employee();
		$emp1->firstname = 'Joe';
		$emp1->lastname = 'Doe';
		$emp1->department = 'Sales';
		$emp1->tasks = [$task1, $task2];
	
		$emp2 = new Employee();
		$emp2->firstname = 'Jane';
		$emp2->lastname = 'Doe';
		$emp2->department = 'IT';
		$emp2->tasks = [$task1, $task3];
	
		$employeesManager->save($emp1);
		$employeesManager->save($emp2);
	
		$tasksManager->delete($task2);
		$related = $mapper->type('i')->query("SELECT COUNT(*) FROM emp_tasks");
		$this->assertEquals(3, $related);
	
		$mapper->close();
	}
	
	public function testManyToManyDeleteEmployee() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
	
		$mapper = $this->getMapper();
		$employeesManager = $mapper->newManager('Acme\Storage\Employee');
		$tasksManager = $mapper->newManager('Acme\Storage\Task');
	
		$task1 = new Task();
		$task1->name = 'Task 1';
		$task1->started = true;
		$task1->startingDate = new \Datetime;
	
		$task2 = new Task();
		$task2->name = 'Task 2';
		$task2->started = false;
		$task2->startingDate = new \Datetime;
	
		$task3 = new Task();
		$task3->name = 'Task 3';
		$task3->started = true;
		$task3->startingDate = new \Datetime;
	
		$emp1 = new Employee();
		$emp1->firstname = 'Joe';
		$emp1->lastname = 'Doe';
		$emp1->department = 'Sales';
		$emp1->tasks = [$task1, $task2];
	
		$emp2 = new Employee();
		$emp2->firstname = 'Jane';
		$emp2->lastname = 'Doe';
		$emp2->department = 'IT';
		$emp2->tasks = [$task1, $task3];
	
		$employeesManager->save($emp1);
		$employeesManager->save($emp2);
	
		$employeesManager->delete($emp2);
		
		$related = $mapper->type('i')->query("SELECT COUNT(*) FROM emp_tasks");
		$this->assertEquals(2, $related);
	
		$mapper->close();
	}
}