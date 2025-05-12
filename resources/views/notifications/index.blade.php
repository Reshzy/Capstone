<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>
            
            @if(Auth::user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                    Mark All as Read
                </button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($notifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="flex items-start p-4 border rounded-lg {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                                    <div class="flex-1">
                                        <div class="flex justify-between">
                                            <h3 class="font-semibold">{{ $notification->data['message'] ?? 'Notification' }}</h3>
                                            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="mt-1 text-sm">
                                            @if(isset($notification->data['pr_number']))
                                                <p>PR Number: {{ $notification->data['pr_number'] }}</p>
                                            @endif
                                            
                                            @if(isset($notification->data['title']))
                                                <p>Title: {{ $notification->data['title'] }}</p>
                                            @endif
                                            
                                            @if(isset($notification->data['requestor']))
                                                <p>Requestor: {{ $notification->data['requestor'] }}</p>
                                            @endif
                                            
                                            @if(isset($notification->data['approver']))
                                                <p>Approver: {{ $notification->data['approver'] }}</p>
                                            @endif
                                            
                                            @if(isset($notification->data['supplier_name']))
                                                <p>Supplier: {{ $notification->data['supplier_name'] }}</p>
                                            @endif
                                            
                                            @if(isset($notification->data['rejection_reason']))
                                                <p>Reason: {{ $notification->data['rejection_reason'] }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2">
                                            <div class="flex items-center space-x-4">
                                                <a href="{{ $notification->data['action_url'] ?? '#' }}" class="text-sm text-blue-600 hover:text-blue-800">
                                                    View Details
                                                </a>
                                                
                                                @if(!$notification->read_at)
                                                    <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}">
                                                        @csrf
                                                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">
                                                            Mark as Read
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-500">Read {{ $notification->read_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">You have no notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 