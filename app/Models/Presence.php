<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Presence
 *
 * @property int $id
 * @property int $is_remote
 * @property int $user_id
 * @property Carbon $day
 * @property int $start
 * @property string $lat_start
 * @property string $long_start
 * @property string $ip_start
 * @property string $browser_start
 * @property string $isp_start
 * @property string $image_start
 * @property int $start_late_five_mins
 * @property int $start_late_more_five_mins
 * @property int $start_late_more_fiveteen_mins
 * @property int $end
 * @property string $lat_end
 * @property string $long_end
 * @property string $ip_end
 * @property string $browser_end
 * @property string $isp_end
 * @property string $image_end
 * @property int $start_marked_by_admin
 * @property int $end_marked_by_system
 * @property int $overtime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Presence extends Model
{
	use SoftDeletes;
	protected $table = 'presences';

	protected $casts = [
		'is_remote' => 'int',
		'user_id' => 'int',
		'day' => 'datetime',
		'start' => 'int',
		'start_late' => 'int',
		'end' => 'int',
		'start_marked_by_admin' => 'int',
		'end_marked_by_system' => 'int',
		'overtime' => 'int'
	];

	protected $fillable = [
		'is_remote',
		'user_id',
		'day',
		'start',
		'lat_start',
		'long_start',
		'ip_start',
		'browser_start',
		'isp_start',
		'image_start',
		'start_late',
		'end',
		'lat_end',
		'long_end',
		'ip_end',
		'browser_end',
		'isp_end',
		'image_end',
		'start_marked_by_admin',
		'end_marked_by_system',
		'overtime'
	];
}
