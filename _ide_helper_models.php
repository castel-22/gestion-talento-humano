<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $employee_id
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon|null $check_in
 * @property \Illuminate\Support\Carbon|null $check_out
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $filename
 * @property string $path
 * @property float|null $size
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Backup whereUpdatedAt($value)
 */
	class Backup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees
 * @property-read int|null $employees_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $place
 * @property \Illuminate\Support\Carbon $date
 * @property-read int|null $participants_count
 * @property string $reason
 * @property string|null $description
 * @property int $supervisor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $participants
 * @property-read \App\Models\User $supervisor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereParticipantsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Deployment whereUpdatedAt($value)
 */
	class Deployment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $deployment_id
 * @property int $employee_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant whereDeploymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeploymentParticipant whereUpdatedAt($value)
 */
	class DeploymentParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property string $first_name
 * @property string $last_name
 * @property string $id_number
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $birth_place
 * @property string|null $marital_status
 * @property string|null $address
 * @property string|null $sector
 * @property string|null $parish
 * @property string|null $personal_phone
 * @property string|null $home_phone
 * @property string|null $email
 * @property string|null $blood_type
 * @property string|null $allergies
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property string|null $education_level
 * @property string|null $degree
 * @property string|null $institution
 * @property string|null $graduation_year
 * @property bool $currently_studying
 * @property string|null $specializations
 * @property string|null $employee_code
 * @property \Illuminate\Support\Carbon $hired_date
 * @property string $position
 * @property string $employment_type
 * @property string|null $shift_group
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Deployment> $deployments
 * @property-read int|null $deployments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmployeeDocument> $documents
 * @property-read int|null $documents_count
 * @property-read string $full_name
 * @property-read float $weekly_hours
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Leave> $leaves
 * @property-read int|null $leaves_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkSchedule> $workSchedules
 * @property-read int|null $work_schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAllergies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereBirthPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereBloodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCurrentlyStudying($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEducationLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmergencyContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmergencyContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmployeeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereHiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereHomePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereInstitution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereParish($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePersonalPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereShiftGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereSpecializations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUserId($value)
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $employee_id
 * @property string $title
 * @property string $file_path
 * @property string $file_name
 * @property string $document_type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereUpdatedAt($value)
 */
	class EmployeeDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $employee_id
 * @property string $start_date
 * @property string $end_date
 * @property string $reason
 * @property string $type
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Leave whereUpdatedAt($value)
 */
	class Leave extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $question
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSecurityAnswer> $answers
 * @property-read int|null $answers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SecurityQuestion whereUpdatedAt($value)
 */
	class SecurityQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSecurityAnswer> $securityAnswers
 * @property-read int|null $security_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Deployment> $supervisedDeployments
 * @property-read int|null $supervised_deployments_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $security_question_id
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SecurityQuestion $question
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer whereSecurityQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSecurityAnswer whereUserId($value)
 */
	class UserSecurityAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $employee_id
 * @property string $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkSchedule whereUpdatedAt($value)
 */
	class WorkSchedule extends \Eloquent {}
}

