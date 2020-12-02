I have done task one. If we take a closer look into it the domain knowledge and business logic are understandable. But when we consider the performance and standard of code, it's in terrible condition.

* There are several issues I have found*
	1. Bad Naming conventions (variable names are not clear)
	2. No commenting (slightly difficult to understand the flow)
	3. Repetition code in every single method
		For example in the “Booking Repository Class”  when we getting the users related jobs and all jobs. The code same repeated multiple times, most of their content is the same.
	4. No exception case  handling anywhere
	5. Multiple time execution of the queries to the database
	6. No Consistant Api response
	7. No consistency in way of coding
	8. Not proper input validations / Declaration
	9. simple queries used in a complex way

---------------------------------------------------

* The good things I have notices *
	1. Fat models, skinny controllers (business logic in the model instead of a controller)
	2. Dependency injection used in controller class constructer. It is very good practice.

---------------------------------------------------

* Suggestions *
	* Api Response:
		the api design should be consistent and standardized with responses containing all kinds of basic information. So for this, I have created a custom API response formatting method in the base controller

	* Repetition of Code:
		-> Traits: Traits are used to declare methods that can be used in multiple classes.
		-> Create a single function and used for all specific tasks
		-> Service: we can also create classes that handle one thing (single responsibility), so that the functionality that the class provides, can be reused in multiple other parts of the application
		-> There should be separate objects formatting functions. For example, when we need all the jobs than after query there should be a function to format the object result like 
		
	* Prefer to use Eloquent overusing Query (There are several queries without Eloquent)

---------------------------------------------------

* Push Notifications *
	The push notification is implemented with Queues and pushes it as a job on our job queue. There might be thousands of push notifications we need to handle in the future So this is the best practice to send push notifications via cron jobs (Queues) instead of waiting for the dispatching response. It will be good when we send notification on thousands of devices.