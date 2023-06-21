<template>
    <div class="flex justify-between text-sm mb-8">
        <div>
            Subscribers
            <div class="text-center">
                    {{stat.followers_count}}
                </div>
        </div>
        <div>
            Likes
            <div class="text-center">
                {{stat.likes_count }}
            </div>
        </div>
        <div>
            Posts
            <div class="text-center">
                {{ stat.posts_count }}
            </div>
        </div>
        <div>
            Comments
            <div class="text-center">
                {{ stat.comments_count }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Stat",
    props:[
        'stat'
    ]
}
</script>

<style scoped>

</style>
