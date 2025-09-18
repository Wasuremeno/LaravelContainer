<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Group;

/**
 * @extends Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        $userIds = User::pluck('id')->toArray();
        $groupIds = Group::pluck('id')->toArray();

        // Decide if sender is user 1 or someone else
        if ($this->faker->boolean(50)) {
            $senderId = $this->faker->randomElement(array_diff($userIds, [1]));
            $receiverId = 1;
        } else {
            $senderId = 1;
            $receiverId = $this->faker->randomElement($userIds);
        }

        $groupId = null;
        if (!empty($groupIds) && $this->faker->boolean(50)) {
            $groupId = $this->faker->randomElement($groupIds);
            $group = Group::with('users')->find($groupId);

            if ($group && $group->users->isNotEmpty()) {
                $senderId = $this->faker->randomElement($group->users->pluck('id')->toArray());
            }
        }

        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}