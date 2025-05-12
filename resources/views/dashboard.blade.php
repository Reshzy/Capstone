<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-semibold mb-4">Welcome, {{ Auth::user()->name }}!</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        @if(Auth::user()->hasRole('requestor'))
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                            <h3 class="text-lg font-semibold text-gray-700">My Purchase Requests</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ App\Models\PurchaseRequest::where('user_id', Auth::id())->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                            <h3 class="text-lg font-semibold text-gray-700">Pending Approval</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ App\Models\PurchaseRequest::where('user_id', Auth::id())->where('status', 'submitted')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-yellow-500 hover:text-yellow-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                            <h3 class="text-lg font-semibold text-gray-700">Approved</h3>
                            <p class="text-3xl font-bold text-green-600">{{ App\Models\PurchaseRequest::where('user_id', Auth::id())->where('status', 'approved')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-green-500 hover:text-green-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                            <h3 class="text-lg font-semibold text-gray-700">Rejected</h3>
                            <p class="text-3xl font-bold text-red-600">{{ App\Models\PurchaseRequest::where('user_id', Auth::id())->where('status', 'rejected')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-red-500 hover:text-red-700 text-sm">View all</a>
                        </div>
                        @endif
                        
                        @if(Auth::user()->hasRole('approver'))
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                            <h3 class="text-lg font-semibold text-gray-700">Pending Approval</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ App\Models\PurchaseRequest::where('status', 'submitted')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                            <h3 class="text-lg font-semibold text-gray-700">Approved by Me</h3>
                            <p class="text-3xl font-bold text-green-600">{{ App\Models\PurchaseRequest::where('approver_id', Auth::id())->where('status', 'approved')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-green-500 hover:text-green-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                            <h3 class="text-lg font-semibold text-gray-700">Rejected by Me</h3>
                            <p class="text-3xl font-bold text-red-600">{{ App\Models\PurchaseRequest::where('approver_id', Auth::id())->where('status', 'rejected')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-red-500 hover:text-red-700 text-sm">View all</a>
                        </div>
                        @endif
                        
                        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('procurement_officer'))
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                            <h3 class="text-lg font-semibold text-gray-700">Total Requests</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ App\Models\PurchaseRequest::count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                            <h3 class="text-lg font-semibold text-gray-700">Pending Approval</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ App\Models\PurchaseRequest::where('status', 'submitted')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-yellow-500 hover:text-yellow-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                            <h3 class="text-lg font-semibold text-gray-700">Approved</h3>
                            <p class="text-3xl font-bold text-green-600">{{ App\Models\PurchaseRequest::where('status', 'approved')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-green-500 hover:text-green-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                            <h3 class="text-lg font-semibold text-gray-700">Rejected</h3>
                            <p class="text-3xl font-bold text-red-600">{{ App\Models\PurchaseRequest::where('status', 'rejected')->count() }}</p>
                            <a href="{{ route('purchase-requests.index') }}" class="text-red-500 hover:text-red-700 text-sm">View all</a>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
                            <h3 class="text-lg font-semibold text-gray-700">Suppliers</h3>
                            <p class="text-3xl font-bold text-indigo-600">{{ App\Models\Supplier::count() }}</p>
                            <a href="{{ route('suppliers.index') }}" class="text-indigo-500 hover:text-indigo-700 text-sm">View all</a>
                        </div>
                        @endif
                    </div>
                    
                    @if(Auth::user()->hasRole('requestor'))
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Purchase Requests</h3>
                            <a href="{{ route('purchase-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Create New
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">PR Number</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(App\Models\PurchaseRequest::where('user_id', Auth::id())->latest()->take(5)->get() as $pr)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">
                                                <a href="{{ route('purchase-requests.show', $pr) }}" class="text-blue-600 hover:text-blue-900">{{ $pr->pr_number }}</a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ number_format($pr->estimated_amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $pr->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $pr->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $pr->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $pr->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($pr->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->created_at->format('M d, Y') }}</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                                            <div class="text-sm leading-5 text-gray-900">No purchase requests found.</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if(Auth::user()->hasRole('approver'))
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Pending Approvals</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">PR Number</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Requestor</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(App\Models\PurchaseRequest::with('user')->where('status', 'submitted')->latest()->take(5)->get() as $pr)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->pr_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->department }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ number_format($pr->estimated_amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ $pr->updated_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <a href="{{ route('purchase-requests.show', $pr) }}" class="text-indigo-600 hover:text-indigo-900">Review</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                                            <div class="text-sm leading-5 text-gray-900">No pending approvals found.</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
